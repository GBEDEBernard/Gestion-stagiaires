@props(['report', 'task', 'user', 'canComment'])

@php
    $sortedReviews = $report->reviews->sortBy('created_at')->values();
    $avatarColors = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
@endphp

<div class="relative inline-block w-full" x-data="chatPopupComponent()" x-cloak>

    {{-- Bouton ouverture — disparaît quand le chat est ouvert --}}
    <button @click="openChat()"
            x-show="!chatOpen"
            class="w-full flex items-center justify-between gap-3 py-3 px-4 bg-gradient-to-r from-indigo-50 to-blue-50 hover:from-indigo-100 hover:to-blue-100 rounded-xl transition text-sm font-semibold text-indigo-700 border border-indigo-200/50 group active:scale-98">
        <div class="flex items-center gap-2">
            <svg class="w-4.5 h-4.5 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Voir la discussion</span>
        </div>
        <span class="bg-indigo-200 text-indigo-700 px-2.5 py-1 rounded-full text-xs font-bold" x-text="messages.length"></span>
    </button>

    {{-- Fenêtre chat --}}
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
                sm:inset-auto sm:bottom-4 sm:right-4 sm:w-[400px] sm:h-[620px] sm:rounded-2xl sm:shadow-2xl">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="text-left min-w-0">
                    <h3 class="font-semibold text-white text-sm">Discussion</h3>
                    <p class="text-white/70 text-xs" x-text="`${messages.length} message${messages.length !== 1 ? 's' : ''}`"></p>
                </div>
            </div>
            <button @click="closeChat()" class="text-white/70 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/10 active:scale-95 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Zone messages --}}
        <div class="flex-1 overflow-y-auto overflow-x-hidden px-4 py-4 bg-gradient-to-b from-gray-50/50 to-white/80 min-h-0"
             id="messages-{{ $report->id }}"
             style="scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.2) transparent; -webkit-overflow-scrolling: touch;">
            <style>
                #messages-{{ $report->id }}::-webkit-scrollbar { width: 4px; }
                #messages-{{ $report->id }}::-webkit-scrollbar-track { background: transparent; }
                #messages-{{ $report->id }}::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 3px; }
            </style>

            <template x-if="messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-center py-16">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-black/50">Aucun message<br><span class="text-black/35">Soyez le premier à commenter</span></p>
                </div>
            </template>

            <template x-for="(message, idx) in messages" :key="message.id">
                <div>
                    {{-- Séparateur de jour --}}
                    <template x-if="idx === 0 || !isSameDay(messages[idx-1], message)">
                        <div class="flex items-center gap-2 my-3">
                            <div class="flex-1 h-px bg-black/8"></div>
                            <span class="text-[9px] font-semibold text-black/35 whitespace-nowrap px-1" x-text="formatDate(message.created_at)"></span>
                            <div class="flex-1 h-px bg-black/8"></div>
                        </div>
                    </template>

                    {{-- Ligne message --}}
                    <div class="flex items-end gap-2 mb-4"
                         :class="message.reviewer_id === {{ $user->id }} ? 'flex-row-reverse' : 'flex-row'">

                        {{-- Avatar (autres) --}}
                        <template x-if="message.reviewer_id !== {{ $user->id }}">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0 shadow-sm self-end"
                                 :style="`background: ${getAvatarColor(message.reviewer?.name || 'Système')}`">
                                <span x-text="(message.reviewer?.name || 'S').substring(0, 2).toUpperCase()"></span>
                            </div>
                        </template>

                        <div class="flex flex-col min-w-0"
                             :class="message.reviewer_id === {{ $user->id }} ? 'items-end' : 'items-start'"
                             style="max-width: calc(100% - 44px);">

                            {{-- Nom expéditeur --}}
                            <template x-if="message.reviewer_id !== {{ $user->id }}">
                                <span class="text-[8px] font-bold text-black/40 px-1 mb-0.5" x-text="message.reviewer?.name || 'Système'"></span>
                            </template>

                            {{-- Bulle --}}
                            <div class="relative">
                                <div x-show="!editingId || editingId !== message.id"
                                     class="px-3 py-2 rounded-2xl shadow-sm text-sm leading-relaxed break-words"
                                     style="max-width: 100%; word-break: break-word; overflow-wrap: anywhere;"
                                     :class="message.reviewer_id === {{ $user->id }}
                                        ? 'bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-br-sm'
                                        : 'bg-gray-200 text-gray-900 rounded-bl-sm'">

                                    {{-- Badge statut (action) --}}
                                    <template x-if="message.action === 'approved'">
                                        <div class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded mb-1"
                                             :class="message.reviewer_id === {{ $user->id }} ? 'bg-white/20 text-white' : 'bg-emerald-300/60 text-emerald-800'">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Approuvé
                                        </div>
                                    </template>
                                    <template x-else-if="message.action === 'rejected'">
                                        <div class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded mb-1"
                                             :class="message.reviewer_id === {{ $user->id }} ? 'bg-white/20 text-white' : 'bg-amber-300/60 text-amber-800'">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            Correction
                                        </div>
                                    </template>

                                    <span x-text="message.comment" class="block"></span>
                                    <template x-if="message.edited_at">
                                        <span class="text-[10px] text-white ml-1">(modifié)</span>
                                    </template>
                                </div>

                                {{-- Éditeur inline --}}
                                <div x-show="editingId === message.id" class="min-w-[200px] mt-1">
                                    <textarea x-model="editContent"
                                              class="w-full px-3 py-2 rounded-2xl border border-indigo-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none"
                                              rows="2"
                                              @keydown.enter.prevent="saveEdit(message.id)"></textarea>
                                    <div class="flex justify-end gap-2 mt-1">
                                        <button @click="cancelEdit" class="text-xs px-2 py-1 rounded bg-gray-200">Annuler</button>
                                        <button @click="saveEdit(message.id)" class="text-xs px-2 py-1 rounded bg-indigo-600 text-white">Enregistrer</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Boutons d'action (toujours visibles, placés sous la bulle, alignés à gauche/droite) --}}
                            <div x-show="message.reviewer_id === {{ $user->id }} && (!editingId || editingId !== message.id)"
                                 class="flex items-center gap-1 mt-1"
                                 :class="message.reviewer_id === {{ $user->id }} ? 'justify-end' : 'justify-start'">
                                <button @click="startEdit(message)"
                                        class="p-1 rounded-md hover:bg-black/5 text-black/50 transition"
                                        title="Modifier">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <button @click="deleteMessage(message.id)"
                                        class="p-1 rounded-md hover:bg-red-50 text-red-400 transition"
                                        title="Supprimer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <span class="text-[9px] text-black/30 px-1 mt-0.5" x-text="formatTime(message.created_at)"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Zone saisie --}}
        @if(!$task->isCompleted() && $canComment)
        <div class="border-t border-black/5 bg-white px-3 py-3 flex-shrink-0 safe-area-bottom">
            <form class="flex items-end gap-2" id="chat-form-{{ $report->id }}" @submit.prevent="submitMessage">
                @csrf
              
                <button type="button"
                        class="flex-shrink-0 w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center text-gray-500 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.485 8.485L7.707 20.707"/>
                    </svg>
                </button>
             
                <textarea name="comment"
                          required
                          rows="1"
                          placeholder="Écris ton message…"
                          class="flex-1 bg-gray-100 rounded-2xl py-2.5 px-4 text-sm leading-relaxed resize-none focus:outline-none focus:bg-gray-200 transition font-medium"
                          style="min-height: 42px; max-height: 100px; overflow-y: auto;"
                          oninput="this.style.height='42px'; this.style.height=Math.min(this.scrollHeight,100)+'px';"
                          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.closest('form').dispatchEvent(new Event('submit'));}">
                </textarea>
                
                <button type="submit"
                        class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 transition shadow-md flex items-center justify-center text-white active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
                    </svg>
                </button>
            </form>
        </div>
        @else
        <div class="border-t border-black/5 bg-gray-50 px-4 py-3 text-center text-xs text-black/40 font-medium flex-shrink-0">
            Cette tâche est terminée
        </div>
        @endif

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    const avatarColors = @js($avatarColors);
    const rawMessages = @js($sortedReviews->values()->toArray());
    const initialMessages = Array.isArray(rawMessages) ? [...rawMessages] : Object.values(rawMessages);

    Alpine.data('chatPopupComponent', () => ({
        chatOpen: false,
        messages: initialMessages,
        editingId: null,
        editContent: '',

        getAvatarColor(name) {
            const n = name || 'S';
            let hash = 0;
            for (let i = 0; i < n.length; i++) hash += n.charCodeAt(i);
            return avatarColors[hash % avatarColors.length];
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            if (date.toDateString() === today.toDateString()) return "Aujourd'hui";
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
            this.$nextTick(() => { this.scrollToBottom(); });
        },

        closeChat() {
            this.chatOpen = false;
            document.body.style.overflow = '';
        },

        scrollToBottom() {
            const el = document.getElementById('messages-{{ $report->id }}');
            if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 100);
        },

        async submitMessage() {
            const form = document.getElementById('chat-form-{{ $report->id }}');
            if (!form) return;

            const textarea = form.querySelector('textarea');
            const comment = textarea.value.trim();
            if (!comment) return;

            const formData = new FormData();
            formData.append('comment', comment);
            formData.append('_token', form.querySelector('[name="_token"]').value);

            try {
                const response = await fetch('{{ route("reports.comments.store", $report->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (!Array.isArray(this.messages)) {
                        this.messages = Object.values(this.messages);
                    }
                    this.messages.push(result.review);
                    textarea.value = '';
                    textarea.style.height = '42px';
                    this.$nextTick(() => { this.scrollToBottom(); });
                } else {
                    const err = await response.json().catch(() => ({}));
                    console.error('Erreur serveur:', err);
                }
            } catch (err) {
                console.error('Erreur réseau:', err);
            }
        },

        startEdit(message) {
            this.editingId = message.id;
            this.editContent = message.comment;
        },

        cancelEdit() {
            this.editingId = null;
            this.editContent = '';
        },

        async saveEdit(id) {
            const newContent = this.editContent.trim();
            if (!newContent) return;

            try {
                const response = await fetch(`/reports/comments/${id}`, {
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
                    const index = this.messages.findIndex(m => m.id === id);
                    if (index !== -1) {
                        this.messages[index].comment = updated.comment;
                        if (updated.edited_at) this.messages[index].edited_at = updated.edited_at;
                    }
                    this.cancelEdit();
                } else {
                    const err = await response.json().catch(() => ({}));
                    alert(err.message || 'Erreur lors de la modification');
                }
            } catch (err) {
                console.error(err);
                alert('Erreur réseau');
            }
        },

        async deleteMessage(id) {
            // ✅ Remplacement du confirm() par SweetAlert2
            const result = await Swal.fire({
                title: 'Supprimer ce message ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/reports/comments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.ok) {
                    this.messages = this.messages.filter(m => m.id !== id);
                    // Optionnel : notification toast de succès
                    Swal.fire({
                        icon: 'success',
                        title: 'Message supprimé',
                        showConfirmButton: false,
                        timer: 1500,
                        toast: true,
                        position: 'top-end'
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