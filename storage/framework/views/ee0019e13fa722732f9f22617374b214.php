<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div x-data="dashboardMessages({
        currentUserId: <?php echo \Illuminate\Support\Js::from($currentUserId)->toHtml() ?>,
        dashboardUrl: <?php echo \Illuminate\Support\Js::from($dashboardUrl)->toHtml() ?>,
        conversations: <?php echo \Illuminate\Support\Js::from($conversations)->toHtml() ?>,
        selectedConversationId: <?php echo \Illuminate\Support\Js::from($selectedConversationId)->toHtml() ?>,
        selectedConversation: <?php echo \Illuminate\Support\Js::from($selectedConversation)->toHtml() ?>,
        selectedMessages: <?php echo \Illuminate\Support\Js::from($selectedMessages)->toHtml() ?>,
        showUrlTemplate: <?php echo \Illuminate\Support\Js::from(route('dashboard.messages.show', ['conversation' => '__conversation__']))->toHtml() ?>,
        storeUrlTemplate: <?php echo \Illuminate\Support\Js::from(route('dashboard.messages.store', ['conversation' => '__conversation__']))->toHtml() ?>,
        readUrlTemplate: <?php echo \Illuminate\Support\Js::from(route('dashboard.messages.read', ['conversation' => '__conversation__']))->toHtml() ?>,
        csrfToken: <?php echo \Illuminate\Support\Js::from(csrf_token())->toHtml() ?>,
    })" x-init="init()"
        class="relative left-1/2 right-1/2 min-h-screen w-screen -translate-x-1/2 bg-[#f5efe2] py-10">
        <div class="mx-auto flex w-full max-w-[1260px] flex-col gap-6 px-4 sm:px-6 lg:px-8">
            <section
                class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-6 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)]">
                <div class="flex flex-wrap items-center justify-between gap-3">

                    <div>
                        <a :href="dashboardUrl"
                            class="inline-flex items-center rounded-full border border-[#d8c7a7] bg-[#fff3df] px-4 py-2 text-sm font-semibold text-[#5f4a36] transition hover:bg-[#f4e4ca]">
                            <span aria-hidden="true" class="mr-1">&larr;</span>
                            Back
                        </a>
                        <br>
                        <br>
                        <h1 class="text-2xl font-bold text-[#3f2d22]">Messages</h1>
                    </div>
                </div>
            </section>

            <section class="grid gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
                <aside
                    class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-4 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)]">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-base font-semibold text-[#3f2d22]">Recent Conversations</h2>
                        <span class="rounded-full bg-[#eef4e6] px-2 py-0.5 text-xs font-semibold text-[#53753e]"
                            x-text="conversations.length"></span>
                    </div>

                    <div class="space-y-2">
                        <template x-if="conversations.length === 0">
                            <p
                                class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-3 text-sm text-[#7b6756]">
                                No conversations yet.
                            </p>
                        </template>

                        <template x-for="conversation in conversations" :key="conversation.id">
                            <button type="button" @click="openConversation(conversation.id)"
                                :class="selectedConversationId === conversation.id ?
                                    'border-[#a0be84] bg-[#eef4e6]' :
                                    'border-[#e4d4bc] bg-[#fffdf8] hover:bg-[#f8efdf]'"
                                class="w-full rounded-xl border p-3 text-left transition">
                                <div class="flex items-start gap-3">
                                    <template x-if="conversation.participant.avatar_url">
                                        <img :src="conversation.participant.avatar_url" alt="Avatar"
                                            class="h-10 w-10 rounded-full border border-[#d5c19e] object-cover">
                                    </template>
                                    <template x-if="!conversation.participant.avatar_url">
                                        <span
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#d5c19e] bg-[#f4e7d1] text-sm font-semibold text-[#5f4a36]"
                                            x-text="initials(conversation.participant.name)"></span>
                                    </template>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="truncate text-sm font-semibold text-[#3f2d22]"
                                                x-text="conversation.participant.name"></p>
                                            <p class="shrink-0 text-[11px] text-[#7b6756]"
                                                x-text="formatTimestamp(conversation.last_message_at)"></p>
                                        </div>
                                        <p class="mt-1 truncate text-xs text-[#7b6756]"
                                            x-text="conversation.last_message || 'No messages yet'"></p>
                                    </div>

                                    <span x-show="conversation.unread_count > 0"
                                        class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#5f8a46] px-1.5 text-[11px] font-semibold text-white"
                                        x-text="conversation.unread_count"></span>
                                </div>
                            </button>
                        </template>
                    </div>
                </aside>

                <article
                    class="rounded-2xl border border-[#dfd0b4] bg-[#fffaf1] p-4 shadow-[0_20px_45px_-30px_rgba(63,45,34,0.55)] sm:p-6">
                    <template x-if="!activeConversation">
                        <div
                            class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">
                            Select a conversation to start chatting.
                        </div>
                    </template>

                    <template x-if="activeConversation">
                        <div>
                            <div class="mb-4 border-b border-[#eadcc3] pb-4">
                                <p class="text-lg font-semibold text-[#3f2d22]"
                                    x-text="activeConversation.participant.name"></p>
                                <p class="text-sm text-[#6f5d52]">Real-time conversation</p>
                            </div>

                            <div x-ref="messagesContainer" class="max-h-[460px] space-y-3 overflow-y-auto pr-1">
                                <template x-if="messages.length === 0">
                                    <p
                                        class="rounded-xl border border-dashed border-[#dac9af] bg-[#fff7eb] p-4 text-sm text-[#7b6756]">
                                        No messages yet. Start the conversation.
                                    </p>
                                </template>

                                <template x-for="message in messages" :key="message.id">
                                    <div :class="Number(message.sender_id) === currentUserId ? 'justify-end' : 'justify-start'"
                                        class="flex">
                                        <div :class="Number(message.sender_id) === currentUserId ? 'bg-[#5f8a46] text-[#f7fff4]' :
                                            'bg-[#f4e7d1] text-[#4d3d30]'"
                                            class="max-w-[85%] rounded-2xl px-4 py-2.5 text-sm">
                                            <p class="whitespace-pre-line break-words" x-text="message.body"></p>
                                            <p :class="Number(message.sender_id) === currentUserId ? 'text-[#d7e8ca]' :
                                                'text-[#806b57]'"
                                                class="mt-1 text-[11px]" x-text="formatTimestamp(message.created_at)">
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <form @submit.prevent="sendMessage" class="mt-4 border-t border-[#eadcc3] pt-4">
                                <label for="messageBody"
                                    class="mb-2 block text-xs font-bold uppercase tracking-[0.08em] text-[#6f5d52]">Message</label>
                                <textarea id="messageBody" x-model="draft" rows="3" maxlength="2000" placeholder="Type your message..."
                                    class="w-full rounded-xl border border-[#dcc9ad] bg-[#fffdf8] px-3.5 py-2.5 text-sm text-[#4d3d30] placeholder:text-[#9b8a79] focus:border-[#a98b64] focus:outline-none focus:ring-2 focus:ring-[#d8c3a2]"></textarea>

                                <div class="mt-3 flex justify-end">
                                    <button type="submit" :disabled="sending || !draft.trim()"
                                        class="inline-flex items-center rounded-full border border-[#4f7740] bg-[#5f8a46] px-5 py-2 text-sm font-semibold text-[#f7fff4] transition hover:bg-[#4f7740] disabled:cursor-not-allowed disabled:opacity-50"
                                        x-text="sending ? 'Sending...' : 'Send Message'"></button>
                                </div>
                            </form>
                        </div>
                    </template>
                </article>
            </section>
        </div>
    </div>

    <script>
        function dashboardMessages(config) {
            return {
                currentUserId: Number(config.currentUserId),
                dashboardUrl: config.dashboardUrl,
                conversations: Array.isArray(config.conversations) ? config.conversations : [],
                selectedConversationId: config.selectedConversationId ? Number(config.selectedConversationId) : null,
                activeConversation: config.selectedConversation ?? null,
                messages: Array.isArray(config.selectedMessages) ? config.selectedMessages : [],
                showUrlTemplate: config.showUrlTemplate,
                storeUrlTemplate: config.storeUrlTemplate,
                readUrlTemplate: config.readUrlTemplate,
                csrfToken: config.csrfToken,
                draft: '',
                sending: false,

                init() {
                    if (this.selectedConversationId === null && this.conversations.length > 0) {
                        this.selectedConversationId = Number(this.conversations[0].id);
                        this.activeConversation = this.conversations[0];
                    }

                    this.subscribe();
                    this.$nextTick(() => this.scrollToBottom());
                },

                subscribe() {
                    if (!window.Echo) {
                        return;
                    }

                    window.Echo
                        .private(`messaging.user.${this.currentUserId}`)
                        .listen('.message.sent', (event) => {
                            this.handleIncomingMessage(event);
                        });
                },

                routeUrl(template, conversationId) {
                    return template.replace('__conversation__', String(conversationId));
                },

                async openConversation(conversationId) {
                    this.selectedConversationId = Number(conversationId);

                    await this.fetchConversation(conversationId);
                    await this.markConversationRead(conversationId);

                    const selected = this.conversations.find((item) => Number(item.id) === Number(conversationId));
                    if (selected) {
                        selected.unread_count = 0;
                    }

                    this.$nextTick(() => this.scrollToBottom());
                },

                async fetchConversation(conversationId) {
                    const response = await fetch(this.routeUrl(this.showUrlTemplate, conversationId), {
                        headers: {
                            Accept: 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    this.messages = Array.isArray(payload.messages) ? payload.messages : [];
                    this.upsertConversation(payload.conversation);

                    const selected = this.conversations.find((item) => Number(item.id) === Number(conversationId));
                    if (selected) {
                        this.activeConversation = selected;
                    }
                },

                async sendMessage() {
                    if (this.sending || this.selectedConversationId === null || !this.draft.trim()) {
                        return;
                    }

                    this.sending = true;

                    try {
                        const response = await fetch(this.routeUrl(this.storeUrlTemplate, this
                        .selectedConversationId), {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                body: this.draft.trim()
                            }),
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();
                        this.upsertConversation(payload.conversation);

                        if (payload.message && !this.messages.some((message) => Number(message.id) === Number(payload
                                .message.id))) {
                            this.messages.push(payload.message);
                        }

                        this.draft = '';
                        this.$nextTick(() => this.scrollToBottom());
                    } finally {
                        this.sending = false;
                    }
                },

                async markConversationRead(conversationId) {
                    await fetch(this.routeUrl(this.readUrlTemplate, conversationId), {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                        credentials: 'same-origin',
                    });
                },

                handleIncomingMessage(event) {
                    const conversationId = Number(event.conversation_id);
                    if (!conversationId) {
                        return;
                    }

                    const conversation = this.conversations.find((item) => Number(item.id) === conversationId);
                    const unreadCounts = event.unread_counts || {};

                    if (conversation) {
                        conversation.last_message = event.last_message ?? conversation.last_message;
                        conversation.last_message_at = event.last_message_at ?? conversation.last_message_at;

                        if (Object.prototype.hasOwnProperty.call(unreadCounts, String(this.currentUserId))) {
                            conversation.unread_count = Number(unreadCounts[String(this.currentUserId)] || 0);
                        }

                        this.moveConversationToTop(conversationId);
                    }

                    if (this.selectedConversationId === conversationId && event.message) {
                        if (!this.messages.some((message) => Number(message.id) === Number(event.message.id))) {
                            this.messages.push(event.message);
                        }

                        if (Number(event.message.sender_id) !== this.currentUserId) {
                            this.markConversationRead(conversationId);

                            const selected = this.conversations.find((item) => Number(item.id) === conversationId);
                            if (selected) {
                                selected.unread_count = 0;
                            }
                        }

                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                upsertConversation(summary) {
                    if (!summary || !summary.id) {
                        return;
                    }

                    const existingIndex = this.conversations.findIndex((item) => Number(item.id) === Number(summary.id));

                    if (existingIndex === -1) {
                        this.conversations.unshift(summary);
                    } else {
                        this.conversations[existingIndex] = {
                            ...this.conversations[existingIndex],
                            ...summary,
                        };
                        this.moveConversationToTop(summary.id);
                    }
                },

                moveConversationToTop(conversationId) {
                    const index = this.conversations.findIndex((item) => Number(item.id) === Number(conversationId));

                    if (index <= 0) {
                        return;
                    }

                    const [conversation] = this.conversations.splice(index, 1);
                    this.conversations.unshift(conversation);
                },

                formatTimestamp(isoString) {
                    if (!isoString) {
                        return '';
                    }

                    const date = new Date(isoString);
                    if (Number.isNaN(date.getTime())) {
                        return '';
                    }

                    return date.toLocaleString([], {
                        month: 'short',
                        day: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                    });
                },

                initials(name) {
                    if (!name) {
                        return 'U';
                    }

                    const parts = String(name).trim().split(/\s+/).filter(Boolean);
                    const first = parts[0]?.[0] ?? '';
                    const second = parts[1]?.[0] ?? '';
                    return `${first}${second}`.toUpperCase() || 'U';
                },

                scrollToBottom() {
                    if (!this.$refs.messagesContainer) {
                        return;
                    }

                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                },
            };
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /home/mistah-regime/tribaltours/resources/views/dashboards/messages.blade.php ENDPATH**/ ?>