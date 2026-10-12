{{--
    T-009 — Discussion GLOBALE UNIQUE de la tâche.
    Bouton flottant (FAB) → ouvre un chat Messenger clair, responsive.
    Un seul fil pour toute l'équipe (producteurs + assignés + admin/superviseur).
    Variables attendues : $task, $user, $chat (['thread' => payload, 'cfg' => urls]).
--}}
@php
    $user = $user ?? auth()->user();
    $canMessage = $task->isParticipant($user->id) || $user->hasAnyRole(['admin', 'superviseur']);
    $chatThread = $chat['thread'] ?? null;
    $chatCfg    = $chat['cfg'] ?? [];
@endphp

<style>
/* ── Chat d'équipe (FAB + overlay) ── */
@keyframes tc-pulse { 0%,100%{opacity:.7;transform:scale(1)} 50%{opacity:0;transform:scale(2)} }
@keyframes tc-fab-in { from{opacity:0;transform:scale(.6)} to{opacity:1;transform:scale(1)} }
.tc-fab { animation: tc-fab-in .25s cubic-bezier(.16,1,.3,1) both; }
.tc-scroll { scrollbar-width: thin; scrollbar-color: rgba(28,62,150,.25) transparent; }
.tc-scroll::-webkit-scrollbar { width: 5px; }
.tc-scroll::-webkit-scrollbar-track { background: transparent; }
.tc-scroll::-webkit-scrollbar-thumb { background: rgba(28,62,150,.3); border-radius: 99px; }
/* Bulles */
.tc-bubble-mine {
    background: #1c3e96;
    color: #fff; border-radius: 14px 14px 4px 14px;
}
.tc-bubble-other {
    background: #fffefa; color: #241f1b; border: 1px solid #dcdad2;
    border-radius: 14px 14px 14px 4px; box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.dark .tc-bubble-other {
    background: #181c24; color: #f8fafc; border-color: #222630;
}
</style>

<div x-data="taskChat(@js($chatThread), @js($chatCfg))" x-init="init()" class="contents">

    {{-- ═══ BOUTON FLOTTANT (FAB) — téléporté au body (le main garde un transform d'animation) ═══ --}}
    <template x-teleport="body">
    <button type="button"
            @click="openChat()"
            aria-label="Ouvrir la discussion d'équipe"
            class="tc-fab fixed bottom-[5.5rem] right-4 z-[75] inline-flex h-11 w-11 sm:h-12 sm:w-12 items-center justify-center rounded-full shadow-lg transition-all hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-blue-300 sm:bottom-5 sm:right-5 bg-[#1c3e96] hover:bg-[#162f73] text-white">
        <template x-if="!open">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m9-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/></svg>
        </template>
        <template x-if="open">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </template>
        {{-- Pastille de non-lus --}}
        <template x-if="!open && unread">
            <span class="absolute -right-0.5 -top-0.5 flex h-3.5 w-3.5 items-center justify-center rounded-full text-[9px] font-bold text-white bg-rose-600 ring-2 ring-white dark:ring-[#131519]"></span>
        </template>
    </button>
    </template>

    {{-- ═══ OVERLAY CHAT (téléporté au body) ═══ --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak
             x-transition.opacity.duration.200ms
             class="fixed inset-0 z-[90] flex items-end justify-center sm:items-center sm:justify-end sm:py-5 sm:pr-5"
             role="dialog" aria-modal="true" aria-label="Discussion d'équipe">

            {{-- Fond sombre avec flou --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="closeChat()"></div>

            {{-- Fenêtre compacte : mobile = plein écran, desktop = fenêtre raffinée --}}
            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-4"
                 @keydown.escape.window="closeChat()"
                 class="relative flex w-full flex-col overflow-hidden bg-[#f1f0ed] dark:bg-[#15181f] text-[#241f1b] dark:text-white border border-[#dcdad2] dark:border-[#222630] shadow-2xl sm:max-w-md sm:rounded-2xl sm:h-[min(82vh,620px)]"
                 style="height:100dvh;">

                {{-- ── Header compact ── --}}
                <header class="z-10 flex items-center gap-3 bg-[#fffefa] dark:bg-[#131519] border-b border-[#dcdad2] dark:border-[#222630] px-3.5 py-2.5 sm:px-4">
                    {{-- Avatars des participants --}}
                    <div class="relative flex shrink-0">
                        <template x-for="(r, i) in recipients.slice(0,3)" :key="r.id">
                            <span class="contents">
                                <template x-if="r.avatar_url">
                                    <img :src="r.avatar_url" :alt="r.name" :title="r.name"
                                         class="h-8 w-8 rounded-full border-2 border-[#fffefa] dark:border-[#131519] object-cover"
                                         :style="'margin-left:'+(i===0?'0':'')+'-8px'">
                                </template>
                                <template x-else>
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#fffefa] dark:border-[#131519] text-[10px] font-bold text-white"
                                          :style="('background:'+avatarColor(r.name))+((i===0)?'':'margin-left:-8px')"
                                          x-text="r.initials"></span>
                                </template>
                            </span>
                        </template>
                        <template x-if="recipients.length > 3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#fffefa] dark:border-[#131519] bg-[#f1f0ed] dark:bg-[#1f242e] text-[10px] font-bold text-[#6e6862] dark:text-slate-400"
                                  x-text="'+'+(recipients.length-3)"></span>
                        </template>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-[14px] font-bold text-[#241f1b] dark:text-white">Discussion d'équipe</h2>
                        <div class="flex items-center gap-1.5">
                            <template x-if="isOpen">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="absolute h-full w-full rounded-full bg-emerald-500 animate-ping"></span>
                                        <span class="relative h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    </span>
                                    en direct
                                </span>
                            </template>
                            <template x-if="isLocked">
                                <span class="text-[11px] text-amber-600 dark:text-amber-400">en attente du premier rapport</span>
                            </template>
                            <template x-if="isClosed">
                                <span class="text-[11px] text-[#6e6862] dark:text-slate-400">clôturée</span>
                            </template>
                        </div>
                    </div>

                    <button type="button" @click="closeChat()" aria-label="Fermer la discussion"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[#6e6862] dark:text-slate-400 transition hover:bg-black/5 dark:hover:bg-white/10 hover:text-[#241f1b] dark:hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </header>

                {{-- ── Corps ── --}}
                <div class="tc-scroll flex-1 overflow-y-auto px-3 py-3 sm:px-3.5 space-y-3">
                    <template x-if="isLocked">
                        <div class="flex h-full flex-col items-center justify-center px-6 text-center">
                            <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 11h14v10H5V11Z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-[#241f1b] dark:text-white">En attente du premier rapport</p>
                            <p class="mt-1 text-xs text-[#6e6862] dark:text-slate-400">La discussion s'ouvre dès le premier dépôt de rapport.</p>
                        </div>
                    </template>

                    <template x-if="!isLocked">
                        <div class="space-y-3">
                            {{-- Rapport épinglé --}}
                            <template x-if="pinned">
                                <div class="mx-0.5 rounded-xl bg-[#fffefa] dark:bg-[#131519] p-3 border border-[#dcdad2] dark:border-[#222630] shadow-xs">
                                    <div class="flex items-start gap-2.5">
                                        <template x-if="pinned.author.avatar_url">
                                            <img :src="pinned.author.avatar_url" :alt="pinned.author.name" class="h-8 w-8 shrink-0 rounded-full object-cover">
                                        </template>
                                        <template x-else>
                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white"
                                                  :style="'background:'+avatarColor(pinned.author.name)" x-text="pinned.author.initials"></span>
                                        </template>
                                        <div class="min-w-0 flex-1">
                                            <div class="mb-1 flex flex-wrap items-center gap-1.5">
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400">Rapport épinglé</span>
                                                <span class="text-[11px] text-[#6e6862] dark:text-slate-400" x-text="pinned.date_human"></span>
                                                <template x-if="pinned.progress !== null">
                                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300" x-text="pinned.progress+'%'"></span>
                                                </template>
                                            </div>
                                            <p class="text-xs font-semibold text-[#241f1b] dark:text-white" x-text="pinned.author.name"></p>
                                            <template x-if="!pinned.is_voice">
                                                <p class="mt-0.5 text-xs leading-5 whitespace-pre-line text-[#241f1b]/80 dark:text-slate-200" x-text="pinned.summary"></p>
                                            </template>
                                            <template x-if="pinned.blockers">
                                                <p class="mt-1.5 rounded-md px-2 py-1 text-[11px] bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40" x-text="pinned.blockers"></p>
                                            </template>
                                            <template x-if="canMessage && isOpen">
                                                <button @click="replyToPinned()" class="mt-1.5 text-xs font-semibold text-[#1c3e96] dark:text-blue-400 hover:underline">↩ Répondre</button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Messages groupés par jour --}}
                            <template x-for="tcgroup in grouped" :key="tcgroup.key">
                                <div class="space-y-2">
                                    <div class="sticky top-0 z-10 flex justify-center py-1">
                                        <span class="rounded-full bg-[#fffefa]/90 dark:bg-[#131519]/90 border border-[#dcdad2] dark:border-[#222630] px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400 backdrop-blur-xs" x-text="tcgroup.label"></span>
                                    </div>
                                    <template x-for="m in tcgroup.items" :key="m.id">
                                        <div>
                                            {{-- Changement de statut --}}
                                            <template x-if="m.type==='status_change'">
                                                <div class="my-2 flex items-center gap-2">
                                                    <div class="h-px flex-1 bg-[#dcdad2] dark:bg-[#222630]"></div>
                                                    <span class="rounded-full px-2.5 py-0.5 text-[10px] font-medium bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] text-[#6e6862] dark:text-slate-400" x-text="m.body"></span>
                                                    <div class="h-px flex-1 bg-[#dcdad2] dark:bg-[#222630]"></div>
                                                </div>
                                            </template>
                                            {{-- Jalon rapport --}}
                                            <template x-if="m.type==='report_jalon'">
                                                <div class="flex justify-center py-1">
                                                    <div class="inline-flex items-center gap-1.5 rounded-full bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] px-2.5 py-0.5 text-[10px] font-medium text-[#6e6862] dark:text-slate-400 shadow-xs">
                                                        <svg class="h-3 w-3 text-[#1c3e96] dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
                                                        <span x-text="m.body"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            {{-- Message --}}
                                            <template x-if="m.type==='message'">
                                                <div class="flex items-end gap-1.5" :class="m.mine?'justify-end':'justify-start'">
                                                    {{-- Photo / avatar de l'auteur --}}
                                                    <template x-if="!m.mine">
                                                        <template x-if="m.user.avatar_url">
                                                            <img :src="m.user.avatar_url" :alt="m.user.name" class="mb-0.5 h-6 w-6 shrink-0 rounded-full object-cover">
                                                        </template>
                                                        <template x-else>
                                                            <span class="mb-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[9px] font-bold text-white"
                                                                  :style="'background:'+avatarColor(m.user.name)" x-text="m.user.initials"></span>
                                                        </template>
                                                    </template>
                                                    <div class="flex max-w-[82%] flex-col" :class="m.mine?'items-end':'items-start'">
                                                        <template x-if="!m.mine">
                                                            <span class="mb-0.5 px-1 text-[10px] font-medium text-[#6e6862] dark:text-slate-400" x-text="m.user.name"></span>
                                                        </template>
                                                        <template x-if="m.parent">
                                                            <div class="mb-1 max-w-full rounded-lg px-2 py-0.5 text-[11px] shadow-xs border-l-2 border-[#1c3e96] bg-[#fffefa] dark:bg-[#181c24] border-t border-r border-b border-[#dcdad2] dark:border-[#222630]" :class="m.mine?'self-end':'self-start'">
                                                                <span class="block font-semibold text-[#241f1b] dark:text-slate-300" x-text="m.parent.user_name"></span>
                                                                <span class="block truncate text-[#6e6862] dark:text-slate-400" x-text="m.parent.excerpt"></span>
                                                            </div>
                                                        </template>
                                                        <div class="px-3 py-1.5 text-xs leading-5 shadow-xs" :class="m.mine?'tc-bubble-mine':'tc-bubble-other'">
                                                            <template x-if="m.body"><p class="whitespace-pre-line" x-text="m.body"></p></template>
                                                            <template x-if="m.attachment&&m.attachment.type==='audio'">
                                                                <audio controls class="mt-1 h-8 max-w-full" :src="m.attachment.url"></audio>
                                                            </template>
                                                            <template x-if="m.attachment&&m.attachment.type==='image'">
                                                                <a :href="m.attachment.url" target="_blank" class="mt-1 block overflow-hidden rounded-lg">
                                                                    <img :src="m.attachment.url" class="max-h-52 w-full object-cover">
                                                                </a>
                                                            </template>
                                                            <template x-if="m.attachment&&m.attachment.type==='file'">
                                                                <a :href="m.attachment.url" target="_blank" class="mt-1 flex items-center gap-1.5 rounded-md px-2 py-1 text-[11px] font-medium bg-black/5 dark:bg-white/10">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a2 2 0 0 0 2 2h4M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"/></svg>
                                                                    <span class="truncate" x-text="m.attachment.name||'Fichier'"></span>
                                                                </a>
                                                            </template>
                                                            <span class="mt-0.5 block text-right text-[9px] opacity-70" :class="m.mine?'text-white':'text-[#6e6862] dark:text-slate-400'" x-text="m.time"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Aucun message --}}
                            <template x-if="!messages.length">
                                <div class="flex min-h-[35vh] flex-col items-center justify-center text-center">
                                    <div class="mb-2.5 flex h-10 w-10 items-center justify-center rounded-xl bg-[#fffefa] dark:bg-[#131519] border border-[#dcdad2] dark:border-[#222630] text-[#6e6862] dark:text-slate-400 shadow-xs">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m9-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/></svg>
                                    </div>
                                    <p class="text-xs font-semibold text-[#241f1b] dark:text-white">Aucun message pour l'instant</p>
                                    <template x-if="canMessage">
                                        <p class="mt-0.5 text-[11px] text-[#6e6862] dark:text-slate-400">Écris le premier message de l'équipe.</p>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- ── Bas : clôturée / composer ── --}}
                <template x-if="isClosed">
                    <div class="px-4 py-2.5 text-center text-xs text-[#6e6862] dark:text-slate-400 bg-[#fffefa] dark:bg-[#131519] border-t border-[#dcdad2] dark:border-[#222630]">
                        Discussion clôturée.{{ $user->hasRole('admin') ? ' Tu peux la rouvrir depuis les actions de la tâche.' : '' }}
                    </div>
                </template>

                @if($canMessage)
                <template x-if="isOpen">
                    <div class="border-t border-[#dcdad2] dark:border-[#222630] bg-[#fffefa] dark:bg-[#131519] px-3 pb-[max(env(safe-area-inset-bottom),10px)] pt-2 sm:px-3.5">
                        {{-- Réponse --}}
                        <template x-if="replyTo">
                            <div class="mb-2 flex items-center gap-2 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] px-2.5 py-1">
                                <span class="text-xs font-bold text-[#1c3e96] dark:text-blue-400">↩</span>
                                <div class="min-w-0 flex-1">
                                    <span class="block text-[9px] font-bold uppercase tracking-wider text-[#6e6862] dark:text-slate-400" x-text="replyTo.user_name"></span>
                                    <span class="block truncate text-xs text-[#241f1b] dark:text-slate-200" x-text="replyTo.excerpt"></span>
                                </div>
                                <button @click="clearReply()" class="rounded p-1 text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white" aria-label="Annuler la réponse">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        {{-- Saisie pièce jointe --}}
                        <template x-if="attachment">
                            <div class="mb-2 flex items-center gap-2 rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] px-2.5 py-1.5">
                                <template x-if="attachment.type==='image'">
                                    <img :src="attachment.preview" class="h-9 w-9 shrink-0 rounded-md object-cover">
                                </template>
                                <template x-else>
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-[#eef1fb] text-[#1c3e96] dark:bg-blue-950/50 dark:text-blue-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a2 2 0 0 0 2 2h4M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"/></svg>
                                    </span>
                                </template>
                                <div class="min-w-0 flex-1">
                                    <span class="block truncate text-xs font-medium text-[#241f1b] dark:text-white" x-text="attachment.name"></span>
                                    <span class="block text-[10px] text-[#6e6862] dark:text-slate-400" x-text="formatSize(attachment.size)"></span>
                                </div>
                                <button type="button" @click="clearAttachment()" class="rounded p-1 text-[#6e6862] hover:text-[#241f1b] dark:hover:text-white" aria-label="Retirer la pièce jointe">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <form @submit.prevent="send()" class="flex items-end gap-1.5">
                            <button type="button" @click="$refs.attachments.click()" :disabled="sending"
                                    aria-label="Joindre un fichier ou une image"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-[#6e6862] dark:text-slate-400 border border-[#dcdad2] dark:border-[#222630] bg-[#faf9f6] dark:bg-[#181c24] hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                            </button>
                            <input type="file" x-ref="attachments" class="hidden"
                                   accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx,.xls,.xlsx,.zip"
                                   @change="onAttach($event)">
                            <textarea x-ref="input" x-model="body" rows="1" placeholder="Écrire un message…"
                                @keydown.enter="if(!$event.shiftKey){$event.preventDefault();send()}"
                                class="flex-1 resize-none rounded-lg bg-[#faf9f6] dark:bg-[#181c24] border border-[#dcdad2] dark:border-[#222630] px-3 py-2 text-xs leading-5 text-[#241f1b] dark:text-white placeholder-[#6e6862] dark:placeholder-slate-500 outline-none transition focus:border-[#1c3e96]"
                                style="max-height:90px;min-height:36px;"></textarea>
                            <button type="submit" :disabled="sending||(!body.trim()&&!attachment)"
                                    aria-label="Envoyer le message"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#1c3e96] hover:bg-[#162f73] text-white transition disabled:opacity-40">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/></svg>
                            </button>
                        </form>
                    </div>
                </template>
                @endif
            </div>
        </div>
    </template>
</div>

<script>
if (typeof window.taskChat !== 'function') {
    window.taskChat = function(initial, cfg) {
        return {
            payload: initial||{task:{},messages:[],recipients:[],pinned_report:null,me:{}},
            cfg, body:'', replyTo:null, sending:false, open:false, attachment:null,
            csrf: document.querySelector('meta[name="csrf-token"]')?.content||'',
            get state()    { return this.payload.task?.discussion_state||'locked'; },
            get isOpen()   { return this.state==='open'; },
            get isClosed() { return this.state==='closed'; },
            get isLocked() { return this.state==='locked'; },
            get pinned()   { return this.payload.pinned_report; },
            get recipients(){ return this.payload.recipients||[]; },
            get unread() {
                const t=this.payload.task, me=this.payload.me;
                return t?.last_message_id && me?.last_read_message_id && t.last_message_id>me.last_read_message_id;
            },
            get messages() {
                const pid=this.pinned?.id;
                return (this.payload.messages||[]).filter(m=>!(m.type==='report_jalon'&&pid&&m.daily_report_id===pid));
            },
            get grouped() {
                const out=[]; let cur=null;
                for(const m of this.messages){ if(!cur||cur.key!==m.day_key){cur={key:m.day_key,label:m.day_label,items:[]};out.push(cur);} cur.items.push(m); }
                return out;
            },
            init() {
                this.$nextTick(()=>this.scrollBottom());
                this.startPolling();
                this.setupEcho();
                this.markRead();
                document.addEventListener('visibilitychange',()=>{ if(!document.hidden) this.refresh(); });
            },
            openChat() {
                this.open=true;
                document.body.style.overflow='hidden';
                this.$nextTick(()=>{ this.refresh(); this.$nextTick(()=>this.scrollBottom()); this.$refs.input?.focus(); });
            },
            closeChat() {
                this.open=false;
                document.body.style.overflow='';
            },
            setupEcho() { if(typeof window.Echo==='undefined') return; const id=this.payload.task?.id; if(!id) return; try{ window.Echo.private(`task.${id}`).listen('message.created',()=>this.refresh()).listen('reaction.added',()=>this.refresh()).listen('message.read',()=>this.refresh()); }catch(e){} },
            startPolling() { setInterval(()=>{ if(!document.hidden) this.refresh(); },4000); },
            async refresh() { try{ const r=await fetch(this.cfg.threadUrl,{headers:{'Accept':'application/json'}}); if(r.ok) this.merge(await r.json()); }catch(e){} },
            merge(data) { const b=this.isAtBottom(),p=this.lastId(); this.payload=data; this.$nextTick(()=>{ if(b||this.lastId()!==p) this.scrollBottom(); }); if(this.lastId()>p) this.markRead(); },
            async send() {
                const text=(this.body||'').trim();
                if((!text&&!this.attachment)||this.sending||!this.isOpen) return;
                this.sending=true;
                const fd=new FormData();
                if(text) fd.append('body',text);
                fd.append('ajax','1');
                if(this.replyTo?.id) fd.append('parent_id',this.replyTo.id);
                if(this.attachment) fd.append('attachment',this.attachment.file);
                try{ const r=await fetch(this.cfg.storeUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json'},body:fd}); if(r.ok){ const d=await r.json(); this.payload.messages.push(d.message); this.payload.task.last_message_id=d.last_message_id; this.body=''; this.replyTo=null; this.clearAttachment(); this.$nextTick(()=>this.scrollBottom()); this.markRead(); } }catch(e){} finally{this.sending=false;}
            },
            onAttach(e) {
                const file=e.target.files?.[0]; if(!file) return;
                e.target.value='';
                const isImage=file.type.startsWith('image/');
                this.attachment={ file, name:file.name, size:file.size, type:isImage?'image':'file', preview:isImage?URL.createObjectURL(file):null };
            },
            clearAttachment() { if(this.attachment?.preview) URL.revokeObjectURL(this.attachment.preview); this.attachment=null; },
            formatSize(bytes) { if(!bytes) return ''; return bytes>1048576?(bytes/1048576).toFixed(1)+' Mo':(bytes/1024).toFixed(1)+' Ko'; },
            setReply(m) { if(!m||m.is_system) return; this.replyTo={id:m.id,user_name:m.mine?'Vous':m.user.name,excerpt:this.excerptOf(m)}; this.$nextTick(()=>this.$refs.input?.focus()); },
            replyToPinned() { if(!this.pinned) return; const j=(this.payload.messages||[]).find(m=>m.type==='report_jalon'&&m.daily_report_id===this.pinned.id); this.replyTo={id:j?.id||null,user_name:this.pinned.author.name,excerpt:this.pinned.is_voice?'Message vocal':(this.pinned.summary||'Rapport')}; this.$nextTick(()=>this.$refs.input?.focus()); },
            clearReply() { this.replyTo=null; },
            excerptOf(m) { if(m.attachment?.type==='audio') return 'Message vocal'; if(m.attachment?.type==='image') return 'Photo'; if(m.attachment?.type==='file') return 'Fichier '+(m.attachment.name||''); return (m.body||'').slice(0,80); },
            async markRead() { const l=this.lastId(); if(!l) return; try{ await fetch(this.cfg.readUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json','Content-Type':'application/x-www-form-urlencoded'},body:'up_to='+l}); }catch(e){} },
            lastId() { const m=this.payload.messages||[]; return m.length?m[m.length-1].id:0; },
            scrollBottom() { const el=this.$refs.scroll; if(el) el.scrollTop=el.scrollHeight; },
            isAtBottom() { const el=this.$refs.scroll; if(!el) return true; return(el.scrollHeight-el.scrollTop-el.clientHeight)<80; },
            avatarColor(n) { n=n||'?'; let h=0; for(let i=0;i<n.length;i++) h=(h*31+n.charCodeAt(i))%360; return 'hsl('+h+' 55% 48%)'; },
            canMessage: @js($canMessage),
        };
    };
}
</script>