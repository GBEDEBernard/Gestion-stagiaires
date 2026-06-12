@props(['report', 'task', 'user', 'canComment'])

@php
    $sortedReviews = $report->reviews->sortBy('created_at');
    $avatarColors = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
@endphp

<div class="relative inline-block w-full" x-data="chatPopupComponent()" x-cloak>

    {{-- Button to open chat --}}
    <button @click="chatOpen = true"
            class="w-full flex items-center justify-between gap-3 py-3 px-4 bg-gradient-to-r from-indigo-50 to-blue-50 hover:from-indigo-100 hover:to-blue-100 rounded-xl transition text-sm font-semibold text-indigo-700 border border-indigo-200/50 group active:scale-98">
        <div class="flex items-center gap-2">
            <svg class="w-4.5 h-4.5 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Voir la discussion</span>
        </div>
        <span class="bg-indigo-200 text-indigo-700 px-2.5 py-1 rounded-full text-xs font-bold" x-text="messages.length"></span>
    </button>

    {{-- Chat Window --}}
    <div x-show="chatOpen"
         @keydown.escape.window="chatOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="fixed bottom-4 right-4 z-50 w-96 h-[620px] rounded-2xl shadow-2xl bg-white flex flex-col overflow-hidden border border-black/5">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-5 py-4 flex items-center justify-between">
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
            <button @click="chatOpen = false" class="text-white/70 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/10 active:scale-95 flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages Container --}}
        <div class="flex-1 overflow-y-auto scroll-smooth px-4 py-4 bg-gradient-to-b from-gray-50/50 to-white/80"
             id="messages-{{ $report->id }}"
             style="scrollbar-width: none; -ms-overflow-style: none;">
            <style>
                #messages-{{ $report->id }}::-webkit-scrollbar { display: none; }
            </style>

            <template x-if="messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-black/50">Aucun message<br><span class="text-black/35">Soyez le premier à commenter</span></p>
                </div>
            </template>

            <template x-for="(message, idx) in messages" :key="message.id">
                <div class="space-y-1.5">
                    {{-- Day separator --}}
                    <template x-if="idx === 0 || !isSameDay(messages[idx-1], message)">
                        <div class="flex items-center gap-2 my-2 pt-1">
                            <div class="flex-1 h-px bg-black/8"></div>
                            <span class="text-[8px] font-semibold text-black/35 whitespace-nowrap px-1" x-text="formatDate(message.created_at)"></span>
                            <div class="flex-1 h-px bg-black/8"></div>
                        </div>
                    </template>

                    {{-- Message --}}
                    <div class="flex items-end gap-2 mb-1" :class="message.reviewer_id === {{ $user->id }} ? 'justify-end' : 'justify-start'" class="group">
                        <template x-if="message.reviewer_id !== {{ $user->id }}">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0 shadow-sm"
                                 :style="`background: ${getAvatarColor(message.reviewer?.name || 'Système')}`">
                                <span x-text="(message.reviewer?.name || 'Système').substring(0, 2).toUpperCase()"></span>
                            </div>
                        </template>

                        <div class="flex flex-col" :class="message.reviewer_id === {{ $user->id }} ? 'items-end' : 'items-start'">
                            <template x-if="message.reviewer_id !== {{ $user->id }}">
                                <span class="text-[8px] font-bold text-black/40 px-2 mb-0.5" x-text="message.reviewer?.name || 'Système'"></span>
                            </template>

                            {{-- Message bubble --}}
                            <div class="px-3 py-1.5 rounded-lg shadow-sm text-sm leading-snug max-w-[280px] w-fit group/msg"
                                 :class="message.reviewer_id === {{ $user->id }}
                                    ? 'bg-gradient-to-br from-indigo-500 to-blue-600 text-white'
                                    : 'bg-gray-200 text-gray-900'">

                                {{-- Status badge --}}
                                <template x-if="message.action === 'approved'">
                                    <div class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded mb-1"
                                         :class="message.reviewer_id === {{ $user->id }} ? 'bg-white/20 text-white' : 'bg-emerald-300/60 text-emerald-800'">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Approuvé
                                    </div>
                                </template>
                                <template x-else-if="message.action === 'rejected'">
                                    <div class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded mb-1"
                                         :class="message.reviewer_id === {{ $user->id }} ? 'bg-white/20 text-white' : 'bg-amber-300/60 text-amber-800'">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Correction
                                    </div>
                                </template>

                                <span x-text="message.comment"></span>
                            </div>

                            {{-- Message actions --}}
                            <template x-if="message.reviewer_id === {{ $user->id }}">
                                <div class="hidden group-hover:flex items-center gap-1 mt-0.5 px-2">
                                    <button class="text-[10px] text-black/40 hover:text-black/70 transition" title="Modifier">✏️</button>
                                    <button class="text-[10px] text-black/40 hover:text-red-600 transition" title="Supprimer">🗑️</button>
                                </div>
                            </template>

                            <span class="text-[7px] text-black/35 px-2 mt-0.5" x-text="formatTime(message.created_at)"></span>
                        </div>

                        <template x-if="message.reviewer_id === {{ $user->id }}">
                            <div class="w-7"></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- Input Area --}}
        @if(!$task->isCompleted() && $canComment)
        <div class="border-t border-black/5 bg-white px-4 py-3 flex-shrink-0">
            <form class="flex items-end gap-2" id="chat-form-{{ $report->id }}" @submit.prevent="submitMessage">
                @csrf

                <button type="button" class="flex-shrink-0 w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center text-gray-600 active:scale-95" title="Ajouter une pièce jointe">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.415a6 6 0 108.485 8.485L7.707 20.707"/>
                    </svg>
                </button>

                <textarea name="comment"
                          required
                          rows="1"
                          placeholder="Écris ton message…"
                          class="flex-1 bg-gray-100 border-0 rounded-full py-2.5 px-4 text-sm leading-relaxed resize-none min-h-[40px] max-h-24 focus:outline-none focus:bg-gray-200 focus:shadow-inner transition font-medium"
                          oninput="this.style.height='40px';this.style.height=Math.min(this.scrollHeight,96)+'px'"
                          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();this.closest('form').submit();}"></textarea>

                <button type="submit" class="flex-shrink-0 w-9 h-9 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 hover:from-indigo-600 hover:to-blue-700 transition shadow-md flex items-center justify-center text-white font-semibold active:scale-95">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M16.6915026,12.4744748 L3.50612381,13.2599618 C3.19218622,13.2599618 3.03521743,13.4170592 3.03521743,13.5741566 L1.15159189,20.0151496 C0.8376543,20.8006365 0.99,21.89 1.77946707,22.52 C2.41,22.99 3.50612381,23.1 4.13399899,22.8429026 L21.714504,14.0454487 C22.6563168,13.5741566 23.1272231,12.6315722 22.9702544,11.6889879 C22.9702544,11.6889879 22.3424279,3.02438996 22.3424279,3.02438996 C22.3424279,2.07179624 21.714504,1.2862818 20.9250659,1.00518406 C20.1356277,0.724086309 19.1946707,0.880983382 18.4052326,1.33225548 L1.77946707,9.87648719 C0.994529095,10.3477792 0.837560256,11.2903635 1.15159189,12.4744748 C1.15159189,12.4744748 2.41,15.2165992 3.03521743,16.8006365 C3.34915502,17.89 4.13399899,18.5139165 5.06580634,18.5139165 C5.73712928,18.5139165 6.25788277,18.2328182 6.77863626,17.5238872 L10.3816578,12.9457667 L16.6915026,12.4744748 Z"/>
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
const avatarColors = @js($avatarColors);

function chatPopupComponent() {
    return {
        chatOpen: false,
        messages: @js($sortedReviews->toArray()),

        getAvatarColor(name) {
            return avatarColors[Object.keys(name).reduce((a,b) => a.charCodeAt(0) + b.charCodeAt(0), 0) % avatarColors.length];
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
            const date = new Date(dateString);
            return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        },

        isSameDay(date1, date2) {
            const d1 = new Date(date1.created_at);
            const d2 = new Date(date2.created_at);
            return d1.toDateString() === d2.toDateString();
        },

        async submitMessage() {
            const form = document.getElementById('chat-form-{{ $report->id }}');
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
                    this.messages.push(result.review);
                    textarea.value = '';
                    textarea.style.height = '40px';

                    this.$nextTick(() => {
                        const messagesDiv = document.getElementById('messages-{{ $report->id }}');
                        setTimeout(() => {
                            messagesDiv.scrollTop = messagesDiv.scrollHeight;
                        }, 100);
                    });
                }
            } catch (err) {
                console.error('Erreur:', err);
            }
        }
    }
}
</script>
