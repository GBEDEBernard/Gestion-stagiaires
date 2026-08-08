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
.tc-scroll { scrollbar-width: thin; scrollbar-color: rgba(0,0,0,.1) transparent; }
.tc-scroll::-webkit-scrollbar { width: 5px; }
.tc-scroll::-webkit-scrollbar-track { background: transparent; }
.tc-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,.12); border-radius: 99px; }
/* Bulles */
.tc-bubble-mine {
    background: linear-gradient(135deg,#6366f1,#4f46e5);
    color:#fff; border-radius:18px 18px 4px 18px;
}
.tc-bubble-other {
    background:#fff; color:#111; border:1px solid rgba(0,0,0,.08);
    border-radius:18px 18px 18px 4px; box-shadow:0 1px 2px rgba(0,0,0,.04);
}
</style>

<div x-data="taskChat(@js($chatThread), @js($chatCfg))" x-init="init()" class="contents">

    {{-- ═══ BOUTON FLOTTANT (FAB) — téléporté au body (le main garde un transform d'animation) ═══ --}}
    <template x-teleport="body">
    <button type="button"
            @click="openChat()"
            aria-label="Ouvrir la discussion d'équipe"
            class="tc-fab fixed bottom-5 right-5 z-[75] inline-flex h-14 w-14 items-center justify-center rounded-full shadow-xl transition-transform hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-300"
            style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;">
        <template x-if="!open">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m9-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/></svg>
        </template>
        <template x-if="open">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </template>
        {{-- Pastille de non-lus --}}
        <template x-if="!open && unread">
            <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full text-[9px] font-bold text-white"
                  style="background:#ef4444;box-shadow:0 0 0 2px #fff;"></span>
        </template>
    </button>
    </template>

    {{-- ═══ OVERLAY CHAT (téléporté au body) ═══ --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak
             x-transition.opacity.duration.200ms
             class="fixed inset-0 z-[90] flex items-end justify-center sm:items-center sm:justify-end sm:py-6 sm:pr-6"
             role="dialog" aria-modal="true" aria-label="Discussion d'équipe">

            {{-- Fond --}}
            <div class="absolute inset-0 bg-black/45 backdrop-blur-sm" @click="closeChat()"></div>

            {{-- Fenêtre : mobile = plein écran, desktop = centrée --}}
            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-4"
                 @keydown.escape.window="closeChat()"
                 class="relative flex w-full flex-col overflow-hidden bg-[#f0f2f5] shadow-2xl sm:max-w-lg sm:rounded-3xl sm:h-[min(88vh,720px)]"
                 style="height:100dvh;">

                {{-- ── Header ── --}}
                <header class="z-10 flex items-center gap-3 bg-white px-4 py-3 shadow-sm sm:px-5">
                    {{-- Avatars des participants --}}
                    <div class="relative flex shrink-0">
                        <template x-for="(r, i) in recipients.slice(0,3)" :key="r.id">
                            <template x-if="r.avatar_url">
                                <img :src="r.avatar_url" :alt="r.name" :title="r.name"
                                     class="h-9 w-9 rounded-full border-2 border-white object-cover"
                                     :style="'margin-left:'+(i===0?'0':'')+'-10px'">
                            </template>
                            <template x-else>
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-white text-[11px] font-bold text-white"
                                      :style="('background:'+avatarColor(r.name))+((i===0)?'':'margin-left:-10px')"
                                      x-text="r.initials"></span>
                            </template>
                        </template>
                        <template x-if="recipients.length > 3">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border-2 border-white bg-slate-100 text-[11px] font-bold text-slate-500"
                                  x-text="'+'+(recipients.length-3)"></span>
                        </template>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-[15px] font-semibold text-slate-900">Discussion d'équipe</h2>
                        <div class="flex items-center gap-1.5">
                            <template x-if="isOpen">
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-medium" style="color:#059669;">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="absolute h-full w-full rounded-full" style="background:#10b981;animation:tc-pulse 1.8s ease-in-out infinite;"></span>
                                        <span class="relative h-1.5 w-1.5 rounded-full" style="background:#10b981;"></span>
                                    </span>
                                    en direct
                                </span>
                            </template>
                            <template x-if="isLocked">
                                <span class="text-[11px] text-amber-600">en attente du premier rapport</span>
                            </template>
                            <template x-if="isClosed">
                                <span class="text-[11px] text-slate-400">clôturée</span>
                            </template>
                        </div>
                    </div>

                    <button type="button" @click="closeChat()" aria-label="Fermer la discussion"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </header>

                {{-- ── Corps ── --}}
                <div class="tc-scroll flex-1 overflow-y-auto px-3 py-3 sm:px-4">
                    <template x-if="isLocked">
                        <div class="flex h-full flex-col items-center justify-center px-6 text-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl" style="background:rgba(245,158,11,.1);">
                                <svg class="h-6 w-6" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 11h14v10H5V11Z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-800">En attente du premier rapport</p>
                            <p class="mt-1 text-xs text-slate-500">La discussion s'ouvre dès le premier dépôt de rapport.</p>
                        </div>
                    </template>

                    <template x-if="!isLocked">
                        <div class="space-y-3">
                            {{-- Rapport épinglé --}}
                            <template x-if="pinned">
                                <div class="mx-1 rounded-2xl bg-white p-3.5 shadow-sm" style="border:1px solid rgba(0,0,0,.06);">
                                    <div class="flex items-start gap-3">
                                        <template x-if="pinned.author.avatar_url">
                                            <img :src="pinned.author.avatar_url" :alt="pinned.author.name" class="h-9 w-9 shrink-0 rounded-full object-cover">
                                        </template>
                                        <template x-else>
                                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-[11px] font-bold text-white"
                                                  :style="'background:'+avatarColor(pinned.author.name)" x-text="pinned.author.initials"></span>
                                        </template>
                                        <div class="min-w-0 flex-1">
                                            <div class="mb-1 flex flex-wrap items-center gap-2">
                                                <span class="text-[10px] font-semibold uppercase tracking-[.14em] text-slate-400">Rapport épinglé</span>
                                                <span class="text-[11px] text-slate-400" x-text="pinned.date_human"></span>
                                                <template x-if="pinned.progress !== null">
                                                    <span class="rounded-md px-1.5 py-0.5 text-[11px] font-semibold" style="background:rgba(16,185,129,.1);color:#059669;" x-text="pinned.progress+'%'"></span>
                                                </template>
                                            </div>
                                            <p class="text-sm font-medium text-slate-800" x-text="pinned.author.name"></p>
                                            <template x-if="!pinned.is_voice">
                                                <p class="mt-0.5 text-sm leading-6 whitespace-pre-line text-slate-600" x-text="pinned.summary"></p>
                                            </template>
                                            <template x-if="pinned.blockers">
                                                <p class="mt-1.5 rounded-lg px-2.5 py-1.5 text-xs" style="background:rgba(245,158,11,.06);color:#b45309;border:1px solid rgba(245,158,11,.15);" x-text="pinned.blockers"></p>
                                            </template>
                                            <template x-if="canMessage && isOpen">
                                                <button @click="replyToPinned()" class="mt-1.5 text-xs font-medium transition" style="color:rgba(79,70,229,.85);"
                                                        @mouseenter="$el.style.color='#4f46e5'" @mouseleave="$el.style.color='rgba(79,70,229,.85)'">↩ Répondre</button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Messages groupés par jour --}}
                            <template x-for="tcgroup in grouped" :key="tcgroup.key">
                                <div class="space-y-2">
                                    <div class="sticky top-0 z-10 flex justify-center py-1.5">
                                        <span class="rounded-full bg-white/95 px-3 py-1 text-[10px] font-semibold uppercase tracking-[.12em] text-slate-500 shadow-sm" x-text="tcgroup.label"></span>
                                    </div>
                                    <template x-for="m in tcgroup.items" :key="m.id">
                                        <div>
                                            {{-- Changement de statut --}}
                                            <template x-if="m.type==='status_change'">
                                                <div class="my-2 flex items-center gap-3">
                                                    <div class="h-px flex-1" style="background:rgba(0,0,0,.08);"></div>
                                                    <span class="rounded-full px-3 py-1 text-[10px] font-medium text-slate-500" style="background:rgba(255,255,255,.9);" x-text="m.body"></span>
                                                    <div class="h-px flex-1" style="background:rgba(0,0,0,.08);"></div>
                                                </div>
                                            </template>
                                            {{-- Jalon rapport --}}
                                            <template x-if="m.type==='report_jalon'">
                                                <div class="flex justify-center py-1.5">
                                                    <div class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-[11px] font-medium text-slate-500 shadow-sm" style="border:1px solid rgba(0,0,0,.05);">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 4h8a2 2 0 0 1 2 2v14l-3-2-3 2-3-2-3 2V6a2 2 0 0 1 2-2Z"/></svg>
                                                        <span x-text="m.body"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            {{-- Message --}}
                                            <template x-if="m.type==='message'">
                                                <div class="flex items-end gap-2" :class="m.mine?'justify-end':'justify-start'">
                                                    {{-- Photo / avatar de l'auteur --}}
                                                    <template x-if="!m.mine">
                                                        <template x-if="m.user.avatar_url">
                                                            <img :src="m.user.avatar_url" :alt="m.user.name" class="mb-0.5 h-7 w-7 shrink-0 rounded-full object-cover">
                                                        </template>
                                                        <template x-else>
                                                            <span class="mb-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white"
                                                                  :style="'background:'+avatarColor(m.user.name)" x-text="m.user.initials"></span>
                                                        </template>
                                                    </template>
                                                    <div class="flex max-w-[80%] flex-col" :class="m.mine?'items-end':'items-start'">
                                                        <template x-if="!m.mine">
                                                            <span class="mb-0.5 px-1 text-[10px] font-medium text-slate-400" x-text="m.user.name"></span>
                                                        </template>
                                                        <template x-if="m.parent">
                                                            <div class="mb-1 max-w-full rounded-xl px-2.5 py-1 text-xs shadow-sm" style="border-left:2px solid #6366f1;background:rgba(255,255,255,.95);" :class="m.mine?'self-end':'self-start'">
                                                                <span class="block font-semibold text-slate-500" x-text="m.parent.user_name"></span>
                                                                <span class="block truncate text-slate-400" x-text="m.parent.excerpt"></span>
                                                            </div>
                                                        </template>
                                                        <div class="px-3.5 py-2 text-sm leading-6 shadow-sm" :class="m.mine?'tc-bubble-mine':'tc-bubble-other'">
                                                            <template x-if="m.body"><p class="whitespace-pre-line" x-text="m.body"></p></template>
                                                            <template x-if="m.attachment&&m.attachment.type==='audio'">
                                                                <audio controls class="mt-1 h-9 max-w-full" :src="m.attachment.url"></audio>
                                                            </template>
                                                            <template x-if="m.attachment&&m.attachment.type==='image'">
                                                                <a :href="m.attachment.url" target="_blank" class="mt-1 block overflow-hidden rounded-xl">
                                                                    <img :src="m.attachment.url" class="max-h-56 w-full object-cover">
                                                                </a>
                                                            </template>
                                                            <template x-if="m.attachment&&m.attachment.type==='file'">
                                                                <a :href="m.attachment.url" target="_blank" class="mt-1 flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs font-medium" style="background:rgba(0,0,0,.06);">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4a2 2 0 0 0 2 2h4M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z"/></svg>
                                                                    <span class="truncate" x-text="m.attachment.name||'Fichier'"></span>
                                                                </a>
                                                            </template>
                                                            <span class="mt-1 block text-right text-[10px] opacity-60" :class="m.mine?'text-white/70':'text-slate-400'" x-text="m.time"></span>
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
                                <div class="flex min-h-[40vh] flex-col items-center justify-center text-center sm:min-h-[300px]">
                                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm">
                                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5m9-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">Aucun message pour l'instant.</p>
                                    <template x-if="canMessage">
                                        <p class="mt-1 text-xs text-slate-400">Écris le premier message de l'équipe.</p>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- ── Bas : clôturée / composer ── --}}
                <template x-if="isClosed">
                    <div class="px-4 py-3 text-center text-xs text-slate-400" style="background:#f0f2f5;">Discussion clôturée.{{ $user->hasRole('admin') ? ' Tu peux la rouvrir depuis les actions de la tâche.' : '' }}</div>
                </template>

                @if($canMessage)
                <template x-if="isOpen">
                    <div class="border-t px-3 pb-[max(env(safe-area-inset-bottom),12px)] pt-2 sm:px-4" style="background:#f0f2f5;border-color:rgba(0,0,0,.06);">
                        {{-- Réponse --}}
                        <template x-if="replyTo">
                            <div class="mb-2 flex items-center gap-2 rounded-xl bg-white px-3 py-1.5 shadow-sm">
                                <span class="text-xs" style="color:#4f46e5;">↩</span>
                                <div class="min-w-0 flex-1">
                                    <span class="block text-[10px] font-semibold uppercase tracking-[.1em] text-slate-400" x-text="replyTo.user_name"></span>
                                    <span class="block truncate text-xs text-slate-500" x-text="replyTo.excerpt"></span>
                                </div>
                                <button @click="clearReply()" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100" aria-label="Annuler la réponse">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        {{-- Saisie --}}
                        <form @submit.prevent="send()" class="flex items-end gap-2">
                            <textarea x-ref="input" x-model="body" rows="1" placeholder="Écrire un message…"
                                @keydown.enter="if(!$event.shiftKey){$event.preventDefault();send()}"
                                class="flex-1 resize-none rounded-full bg-white px-4 py-2.5 text-sm leading-5 shadow-sm outline-none transition focus:ring-2 focus:ring-indigo-300"
                                style="max-height:110px;min-height:42px;border:1px solid rgba(0,0,0,.06);color:#111;"></textarea>
                            <button type="submit" :disabled="sending||!body.trim()"
                                    aria-label="Envoyer le message"
                                    class="flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-full text-white shadow-lg transition hover:scale-105 active:scale-95 disabled:opacity-40"
                                    style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 12-2.7-8.7a.5.5 0 0 1 .67-.6l16.5 8.25a.5.5 0 0 1 0 .9L3.97 20.1a.5.5 0 0 1-.67-.6L6 12Zm0 0h6"/></svg>
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
            cfg, body:'', replyTo:null, sending:false, open:false,
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
                const text=this.body.trim(); if(!text||this.sending||!this.isOpen) return; this.sending=true;
                const fd=new FormData(); fd.append('body',text); fd.append('ajax','1'); if(this.replyTo?.id) fd.append('parent_id',this.replyTo.id);
                try{ const r=await fetch(this.cfg.storeUrl,{method:'POST',headers:{'X-CSRF-TOKEN':this.csrf,'Accept':'application/json'},body:fd}); if(r.ok){ const d=await r.json(); this.payload.messages.push(d.message); this.payload.task.last_message_id=d.last_message_id; this.body=''; this.replyTo=null; this.$nextTick(()=>this.scrollBottom()); this.markRead(); } }catch(e){} finally{this.sending=false;}
            },
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