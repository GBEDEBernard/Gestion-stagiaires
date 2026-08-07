@props(['report', 'task', 'user', 'canComment'])

@php
    $sortedReviews = $report->reviews->sortBy('created_at')->values();
    $sortedReviews->each(function ($r) {
        if ($r->reviewer) {
            $r->reviewer->setAttribute('avatar_url', $r->reviewer->avatar ? \Storage::url($r->reviewer->avatar) : null);
        }
    });
    $avatarColors = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];

    $storeUrl         = route('reports.comments.store', $report->id);
    $updateUrlPattern = route('reports.comments.update', ['review' => '__ID__']);
    $deleteUrlPattern = route('reports.comments.destroy', ['review' => '__ID__']);
@endphp

<style>
    /* ── Chat popup : adaptation dark mode ── */
    .chat-input-area {
        background: #f3f4f6;
        color: #111827;
        border: 1.5px solid transparent;
        transition: background .15s, border-color .15s;
    }
    .chat-input-area::placeholder { color: rgba(17,24,39,.40); }
    .chat-input-area:focus {
        background: #e9eaec;
        border-color: #c7d2fe;
        outline: none;
    }

    /* Dark : fond légèrement clair, texte blanc */
    .ws-root.dark .chat-input-area,
    .dark .ws-root .chat-input-area {
        background: rgba(255,255,255,.10);
        color: #ffffff;
        border-color: rgba(255,255,255,.12);
    }
    .ws-root.dark .chat-input-area::placeholder,
    .dark .ws-root .chat-input-area::placeholder {
        color: rgba(255,255,255,.42);
    }
    .ws-root.dark .chat-input-area:focus,
    .dark .ws-root .chat-input-area:focus {
        background: rgba(255,255,255,.14);
        border-color: rgba(165,180,252,.50);
        color: #ffffff;
    }

    /* Zone de saisie fond dark */
    .ws-root.dark .chat-footer,
    .dark .ws-root .chat-footer {
        background: #18181b;
        border-color: rgba(255,255,255,.08);
    }

    /* Bouton pièce jointe dark */
    .ws-root.dark .chat-attach-btn,
    .dark .ws-root .chat-attach-btn {
        background: rgba(255,255,255,.10);
        color: rgba(255,255,255,.70);
    }
    .ws-root.dark .chat-attach-btn:hover,
    .dark .ws-root .chat-attach-btn:hover {
        background: rgba(255,255,255,.16);
        color: #ffffff;
    }

    /* Fenêtre chat fond dark */
    .ws-root.dark .chat-modal,
    .dark .ws-root .chat-modal {
        background: #18181b;
        border-color: rgba(255,255,255,.08);
    }

    /* Zone messages dark */
    .ws-root.dark .chat-messages-area,
    .dark .ws-root .chat-messages-area {
        background: linear-gradient(to bottom, rgba(255,255,255,.025), rgba(255,255,255,.01));
    }

    /* Bulles "autres" dark */
    .ws-root.dark .bubble-other,
    .dark .ws-root .bubble-other {
        background: rgba(255,255,255,.12);
        color: #ffffff;
    }

    /* Séparateurs de jour dark */
    .ws-root.dark .day-sep-line,
    .dark .ws-root .day-sep-line {
        background: rgba(255,255,255,.10);
    }
    .ws-root.dark .day-sep-label,
    .dark .ws-root .day-sep-label {
        color: rgba(255,255,255,.40);
    }

    /* Nom expéditeur dark */
    .ws-root.dark .sender-name,
    .dark .ws-root .sender-name {
        color: rgba(255,255,255,.55);
    }

    /* Horodatage dark */
    .ws-root.dark .msg-time,
    .dark .ws-root .msg-time {
        color: rgba(255,255,255,.32);
    }

    /* Bouton "Voir la discussion" dark */
    .ws-root.dark .chat-open-btn,
    .dark .ws-root .chat-open-btn {
        background: linear-gradient(to right, rgba(99,102,241,.18), rgba(59,130,246,.18));
        border-color: rgba(99,102,241,.35);
        color: #a5b4fc;
    }
    .ws-root.dark .chat-open-btn:hover,
    .dark .ws-root .chat-open-btn:hover {
        background: linear-gradient(to right, rgba(99,102,241,.28), rgba(59,130,246,.28));
    }
    .ws-root.dark .chat-open-btn .chat-count-badge,
    .dark .ws-root .chat-open-btn .chat-count-badge {
        background: rgba(99,102,241,.35);
        color: #c7d2fe;
    }

    /* Éditeur inline dark */
    .ws-root.dark .inline-edit-textarea,
    .dark .ws-root .inline-edit-textarea {
        background: rgba(255,255,255,.08);
        border-color: rgba(165,180,252,.40);
        color: #ffffff;
    }
    .ws-root.dark .inline-edit-cancel,
    .dark .ws-root .inline-edit-cancel {
        background: rgba(255,255,255,.10);
        color: rgba(255,255,255,.75);
    }

    /* Message "tâche terminée" dark */
    .ws-root.dark .chat-closed-notice,
    .dark .ws-root .chat-closed-notice {
        background: rgba(255,255,255,.03);
        border-color: rgba(255,255,255,.07);
        color: rgba(255,255,255,.40);
    }

    /* Mobile : empêcher le scroll du body derrière le chat */
    .chat-modal {
        overscroll-behavior: contain;
    }

    /* Ajustement safe-area pour le footer */
    .pb-safe {
        padding-bottom: max(0.75rem, env(safe-area-inset-bottom, 0.75rem));
    }
</style>

<div class="relative inline-block w-full" x-data="chatPopupComponent({
    storeUrl: '{{ $storeUrl }}',
    updateUrlPattern: '{{ $updateUrlPattern }}',
    deleteUrlPattern: '{{ $deleteUrlPattern }}'
})" x-cloak>

    {{-- ── Bouton ouverture ── --}}
    <button @click="openChat()"
            x-show="!chatOpen"
            class="chat-open-btn w-full flex items-center justify-between gap-3 py-3 px-4
                   bg-gradient-to-r from-indigo-50 to-blue-50 hover:from-indigo-100 hover:to-blue-100
                   rounded-xl transition text-sm font-semibold text-indigo-700
                   border border-indigo-200/50 group active:scale-[.98]">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Voir la discussion</span>
        </div>
        <span class="chat-count-badge bg-indigo-200 text-indigo-700 px-2.5 py-1 rounded-full text-xs font-bold"
              x-text="messages.length"></span>
    </button>

    {{-- ── Fenêtre chat ── téléportée en dehors des ancêtres animés (évite les soucis de position: fixed) --}}
    <template x-teleport="body">
        <div x-show="chatOpen"
             @keydown.escape.window="closeChat()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="chat-modal fixed z-50 flex flex-col bg-white overflow-hidden border border-black/5
                     inset-0 rounded-none
                     sm:inset-auto sm:bottom-4 sm:right-4 sm:w-[400px] sm:max-h-[85vh] sm:rounded-2xl sm:shadow-2xl"
             x-trap.noscroll="chatOpen">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-4 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="text-left min-w-0">
                        <h3 class="font-semibold text-white text-sm">Discussion</h3>
                        <p class="text-white/70 text-xs"
                           x-text="`${messages.length} message${messages.length !== 1 ? 's' : ''}`"></p>
                    </div>
                </div>
                <button @click="closeChat()"
                        class="text-white/70 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/10 active:scale-95 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ── Zone messages ── --}}
            <div class="chat-messages-area flex-1 overflow-y-auto overflow-x-hidden px-4 py-4
                        bg-gradient-to-b from-gray-50/50 to-white/80 min-h-0"
                 id="messages-{{ $report->id }}"
                 style="scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.2) transparent; -webkit-overflow-scrolling: touch;">
                <style>
                    #messages-{{ $report->id }}::-webkit-scrollbar { width: 4px; }
                    #messages-{{ $report->id }}::-webkit-scrollbar-track { background: transparent; }
                    #messages-{{ $report->id }}::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 3px; }
                </style>

                {{-- Vide --}}
                <template x-if="messages.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center py-16">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-medium text-black/50">
                            Aucun message<br>
                            <span class="text-black/35">Soyez le premier à commenter</span>
                        </p>
                    </div>
                </template>

                {{-- Liste messages --}}
                <template x-for="(message, idx) in messages" :key="message.id">
                    <div>
                        {{-- Séparateur de jour --}}
                        <template x-if="idx === 0 || !isSameDay(messages[idx-1], message)">
                            <div class="flex items-center gap-2 my-3">
                                <div class="day-sep-line flex-1 h-px bg-black/8"></div>
                                <span class="day-sep-label text-[9px] font-semibold text-black/35 whitespace-nowrap px-1"
                                      x-text="formatDate(message.created_at)"></span>
                                <div class="day-sep-line flex-1 h-px bg-black/8"></div>
                            </div>
                        </template>

                        {{-- Ligne message --}}
                        <div class="flex items-end gap-2 mb-4"
                             :class="message.reviewer_id === {{ $user->id }} ? 'flex-row-reverse' : 'flex-row'">

                            {{-- Avatar (autres) --}}
                            <template x-if="message.reviewer_id !== {{ $user->id }}">
                                <template x-if="message.reviewer?.avatar_url">
                                    <img :src="message.reviewer.avatar_url"
                                         :alt="message.reviewer?.name || 'S'"
                                         class="w-7 h-7 rounded-full object-cover flex-shrink-0 shadow-sm self-end">
                                </template>
                                <template x-if="!message.reviewer?.avatar_url">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0 shadow-sm self-end"
                                         :style="`background: ${getAvatarColor(message.reviewer?.name || 'Système')}`">
                                        <span x-text="(message.reviewer?.name || 'S').substring(0, 2).toUpperCase()"></span>
                                    </div>
                                </template>
                            </template>

                            <div class="flex flex-col min-w-0"
                                 :class="message.reviewer_id === {{ $user->id }} ? 'items-end' : 'items-start'"
                                 style="max-width: calc(100% - 44px);">

                                {{-- Nom expéditeur --}}
                                <template x-if="message.reviewer_id !== {{ $user->id }}">
                                    <span class="sender-name text-[8px] font-bold text-black/40 px-1 mb-0.5"
                                          x-text="message.reviewer?.name || 'Système'"></span>
                                </template>

                                {{-- Bulle --}}
                                <div class="relative">
                                    <div x-show="!editingId || editingId !== message.id"
                                         class="px-3 py-2 rounded-2xl shadow-sm text-sm leading-relaxed break-words"
                                         style="max-width: 100%; word-break: break-word; overflow-wrap: anywhere;"
                                         :class="message.reviewer_id === {{ $user->id }}
                                            ? 'bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-br-sm'
                                            : 'bubble-other bg-gray-200 text-gray-900 rounded-bl-sm'">

                                        {{-- Badge statut approuvé --}}
                                        <template x-if="message.action === 'approved'">
                                            <div class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded mb-1"
                                                 :class="message.reviewer_id === {{ $user->id }} ? 'bg-white/20 text-white' : 'bg-emerald-300/60 text-emerald-800'">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Approuvé
                                            </div>
                                        </template>
                                        {{-- Badge statut correction --}}
                                        <template x-else-if="message.action === 'rejected'">
                                            <div class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded mb-1"
                                                 :class="message.reviewer_id === {{ $user->id }} ? 'bg-white/20 text-white' : 'bg-amber-300/60 text-amber-800'">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Correction
                                            </div>
                                        </template>

                                        <span x-text="message.comment" class="block"></span>

                                        <template x-if="message.attachment_url">
                                            <div class="mt-2">
                                                <template x-if="message.attachment_type === 'image'">
                                                    <a :href="message.attachment_url" target="_blank"
                                                       class="block rounded-lg overflow-hidden border border-black/10 hover:opacity-90 transition">
                                                        <img :src="message.attachment_url"
                                                             :alt="message.attachment_name || 'Image'"
                                                             class="max-w-full h-auto max-h-48 object-cover rounded-lg">
                                                    </a>
                                                </template>
                                                <template x-if="message.attachment_type === 'file'">
                                                    <a :href="message.attachment_url" target="_blank"
                                                       class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-black/5 hover:bg-black/10 transition text-xs font-medium">
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        <span class="truncate" x-text="message.attachment_name || 'Fichier'"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="message.edited_at">
                                            <span class="text-[10px] opacity-60 ml-1">(modifié)</span>
                                        </template>
                                    </div>

                                    {{-- Éditeur inline --}}
                                    <div x-show="editingId === message.id" class="min-w-0 max-w-[90vw] sm:max-w-xs mt-1">
                                        <textarea x-model="editContent"
                                                  class="inline-edit-textarea w-full px-3 py-2 rounded-2xl border border-indigo-300
                                                         focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none
                                                         bg-white text-gray-900"
                                                  rows="2"
                                                  @keydown.enter.prevent="saveEdit(message.id)"></textarea>
                                        <div class="flex justify-end gap-2 mt-1">
                                            <button @click="cancelEdit"
                                                    class="inline-edit-cancel text-xs px-2 py-1 rounded bg-gray-200 text-gray-700">
                                                Annuler
                                            </button>
                                            <button @click="saveEdit(message.id)"
                                                    class="text-xs px-2 py-1 rounded bg-indigo-600 text-white">
                                                Enregistrer
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Boutons modifier / supprimer --}}
                                <div x-show="message.reviewer_id === {{ $user->id }} && (!editingId || editingId !== message.id)"
                                     class="flex items-center gap-1 mt-1"
                                     :class="message.reviewer_id === {{ $user->id }} ? 'justify-end' : 'justify-start'">
                                    <button @click="startEdit(message)"
                                            class="p-1 rounded-md hover:bg-black/5 text-black/50 transition" title="Modifier">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button @click="deleteMessage(message.id)"
                                            class="p-1 rounded-md hover:bg-red-50 text-red-400 transition" title="Supprimer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <span class="msg-time text-[9px] text-black/30 px-1 mt-0.5"
                                      x-text="formatTime(message.created_at)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- ── Zone de saisie ── --}}
            @if(!$task->isCompleted() && $canComment)
            <div class="chat-footer border-t border-black/5 bg-white px-3 pt-3 pb-safe flex-shrink-0 safe-area-bottom">

                <template x-if="attachedFile">
                    <div class="flex items-center gap-2 px-2.5 py-1.5 mb-2 bg-gray-100 rounded-lg text-xs text-gray-700 min-w-0">
                        <template x-if="attachedPreview">
                            <img :src="attachedPreview"
                                 class="w-8 h-8 rounded object-cover flex-shrink-0 shadow-sm">
                        </template>
                        <template x-if="!attachedPreview">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.485 8.485L7.707 20.707"/>
                            </svg>
                        </template>
                        <span class="truncate flex-1" x-text="attachedFile.name"></span>
                        <button type="button" @click="removeAttachment()"
                                class="p-0.5 rounded hover:bg-black/10 text-gray-500 hover:text-red-500 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>

                <form class="flex items-end gap-2"
                      id="chat-form-{{ $report->id }}"
                      @submit.prevent="submitMessage">
                    @csrf

                    {{-- Bouton pièce jointe --}}
                    <button type="button"
                            @click="$refs.fileInput.click()"
                            class="chat-attach-btn flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200
                                   transition flex items-center justify-center text-gray-500 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.485 8.485L7.707 20.707"/>
                        </svg>
                    </button>

                    <input type="file"
                           id="chat-file-{{ $report->id }}"
                           name="attachment"
                           x-ref="fileInput"
                           accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip"
                           class="hidden"
                           @change="
                               attachedFile = $event.target.files[0];
                               attachedPreview = null;
                               if (attachedFile && attachedFile.type.startsWith('image/')) {
                                   const reader = new FileReader();
                                   reader.onload = e => attachedPreview = e.target.result;
                                   reader.readAsDataURL(attachedFile);
                               }
                           ">

                    {{-- Textarea saisie — classe chat-input-area pour le dark mode ── --}}
                    <textarea name="comment"
                              required
                              rows="1"
                              placeholder="Écris ton message…"
                              class="chat-input-area flex-1 rounded-2xl py-2.5 px-4 text-sm leading-relaxed
                                     resize-none font-medium"
                              style="min-height: 42px; max-height: 100px; overflow-y: auto;"
                              oninput="this.style.height='42px'; this.style.height=Math.min(this.scrollHeight,100)+'px';"
                              onkeydown="if(event.key==='Enter'&&!event.shiftKey){
                                  event.preventDefault();
                                  this.closest('form').dispatchEvent(new Event('submit'));
                              }">
                    </textarea>

                    {{-- Bouton envoyer --}}
                    <button type="submit"
                            class="flex-shrink-0 w-10 h-10 rounded-full
                                   bg-gradient-to-r from-indigo-500 to-blue-600
                                   hover:from-indigo-600 hover:to-blue-700
                                   transition shadow-md flex items-center justify-center text-white active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
                        </svg>
                    </button>
                </form>
            </div>
            @else
            <div class="chat-closed-notice border-t border-black/5 bg-gray-50 px-4 py-3 text-center
                        text-xs text-black/40 font-medium flex-shrink-0">
                Cette tâche est terminée
            </div>
            @endif

        </div>
    </template>

</div>

<script>
document.addEventListener('alpine:init', () => {
    const avatarColors  = @js($avatarColors);
    const rawMessages   = @js($sortedReviews->values()->toArray());
    const initialMessages = Array.isArray(rawMessages) ? [...rawMessages] : Object.values(rawMessages);

    Alpine.data('chatPopupComponent', (config) => ({
        chatOpen: false,
        messages: initialMessages,
        editingId: null,
        editContent: '',
        attachedFile: null,
        attachedPreview: null,
        storeUrl:         config.storeUrl,
        updateUrlPattern: config.updateUrlPattern,
        deleteUrlPattern: config.deleteUrlPattern,

        removeAttachment() {
            this.attachedFile = null;
            this.attachedPreview = null;
            const input = document.getElementById('chat-file-{{ $report->id }}');
            if (input) input.value = '';
        },

        getAvatarColor(name) {
            const n = name || 'S';
            let hash = 0;
            for (let i = 0; i < n.length; i++) hash += n.charCodeAt(i);
            return avatarColors[hash % avatarColors.length];
        },

        formatDate(dateString) {
            const date      = new Date(dateString);
            const today     = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            if (date.toDateString() === today.toDateString())     return "Aujourd'hui";
            if (date.toDateString() === yesterday.toDateString()) return 'Hier';
            return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
        },

        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        },

        isSameDay(msg1, msg2) {
            if (!msg1 || !msg2 || !msg1.created_at || !msg2.created_at) return false;
            return new Date(msg1.created_at).toDateString() === new Date(msg2.created_at).toDateString();
        },

        openChat() {
            this.chatOpen = true;
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            this.$nextTick(() => { this.scrollToBottom(); });
        },

        closeChat() {
            this.chatOpen = false;
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        },

        scrollToBottom() {
            const el = document.getElementById('messages-{{ $report->id }}');
            if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 100);
        },

        async submitMessage() {
            const form = document.getElementById('chat-form-{{ $report->id }}');
            if (!form) return;

            const textarea = form.querySelector('textarea');
            const comment  = textarea.value.trim();
            if (!comment) return;

            const formData = new FormData();
            formData.append('comment', comment);
            formData.append('_token', form.querySelector('[name="_token"]').value);

            const fileInput = document.getElementById('chat-file-{{ $report->id }}');
            if (fileInput && fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            try {
                const response = await fetch(this.storeUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    const result = await response.json();
                    this.messages.push(result.review);
                    textarea.value       = '';
                    textarea.style.height = '42px';
                    this.attachedFile = null;
                    this.attachedPreview = null;
                    if (fileInput) fileInput.value = '';
                    this.$nextTick(() => { this.scrollToBottom(); });
                } else {
                    const err = await response.json().catch(() => ({}));
                    console.error('Erreur serveur :', err);
                    Swal.fire('Erreur', err.message || "Impossible d'envoyer le message", 'error');
                }
            } catch (err) {
                console.error('Erreur réseau :', err);
                Swal.fire('Erreur', 'Problème de connexion', 'error');
            }
        },

        startEdit(message) {
            this.editingId   = message.id;
            this.editContent = message.comment;
        },

        cancelEdit() {
            this.editingId   = null;
            this.editContent = '';
        },

        async saveEdit(id) {
            const newContent = this.editContent.trim();
            if (!newContent) return;

            const url = this.updateUrlPattern.replace('__ID__', id);

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ comment: newContent })
                });

                if (response.ok) {
                    const updated = await response.json();
                    const index   = this.messages.findIndex(m => m.id === id);
                    if (index !== -1) {
                        this.messages[index].comment = updated.comment;
                        if (updated.edited_at) this.messages[index].edited_at = updated.edited_at;
                    }
                    this.cancelEdit();
                } else {
                    const err = await response.json().catch(() => ({}));
                    Swal.fire('Erreur', err.message || 'Impossible de modifier', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Erreur', 'Problème de connexion', 'error');
            }
        },

        async deleteMessage(id) {
            const result = await Swal.fire({
                title: 'Supprimer ce message ?',
                text:  "Cette action est irréversible.",
                icon:  'warning',
                showCancelButton:    true,
                confirmButtonColor:  '#ef4444',
                cancelButtonColor:   '#6b7280',
                confirmButtonText:   'Oui, supprimer',
                cancelButtonText:    'Annuler'
            });

            if (!result.isConfirmed) return;

            const url = this.deleteUrlPattern.replace('__ID__', id);

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    this.messages = this.messages.filter(m => m.id !== id);
                    Swal.fire({
                        icon: 'success', title: 'Message supprimé',
                        showConfirmButton: false, timer: 1500,
                        toast: true, position: 'top-end'
                    });
                } else {
                    const err = await response.json().catch(() => ({}));
                    Swal.fire('Erreur', err.message || 'Impossible de supprimer', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Erreur', 'Problème de connexion', 'error');
            }
        }
    }));
});
</script>