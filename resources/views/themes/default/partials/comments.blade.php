{{-- Blok komentar reusable untuk berita & halaman statis. Variabel: $post (model Post), $comments (root approved + replies). Form hanya tampil bila $post->allow_comments. --}}
@if($post->allow_comments || $comments->isNotEmpty())
    <section id="komentar" class="mt-10 scroll-mt-24" x-data="{ parentId: null, parentName: '' }">
        @include('themes.default.partials.section-title', [
            'title' => 'Komentar ('.($comments->count() + $comments->sum(fn ($c) => $c->replies->count())).')',
        ])

        @if(session('comment_status'))
            <div class="mb-6 rounded-sm border border-primary-200 bg-primary-50 px-4 py-3 text-sm font-medium text-primary-800">
                {{ session('comment_status') }}
            </div>
            {{-- Gulir halus ke blok komentar setelah kirim (redirect membawa #komentar) --}}
            <script>
                window.addEventListener('load', () => {
                    document.getElementById('komentar')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            </script>
        @endif

        {{-- Daftar komentar --}}
        <div class="space-y-4">
            @forelse($comments as $comment)
                <div class="rounded-sm border border-slate-300 bg-white p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-100 font-bold text-primary-800">
                            {{ strtoupper(mb_substr($comment->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5">
                                <span class="font-bold text-slate-900">{{ $comment->name }}</span>
                                <span class="text-xs text-slate-400">{{ tanggal_id($comment->created_at, 'j F Y H.i') }}</span>
                            </div>
                            <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $comment->body }}</p>
                            @if($post->allow_comments)
                                <button type="button"
                                    @click="parentId = {{ $comment->id }}; parentName = @js($comment->name); $nextTick(() => { document.getElementById('form-komentar').scrollIntoView({ behavior: 'smooth' }); document.getElementById('comment-name').focus(); })"
                                    class="mt-2 text-xs font-semibold text-primary-700 hover:text-primary-900">
                                    Balas
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Balasan --}}
                    @if($comment->replies->isNotEmpty())
                        <div class="ml-6 mt-4 space-y-4 border-l-2 border-primary-100 pl-4 sm:ml-12">
                            @foreach($comment->replies as $reply)
                                <div class="flex items-start gap-3">
                                    <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">
                                        {{ strtoupper(mb_substr($reply->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5">
                                            <span class="text-sm font-bold text-slate-900">{{ $reply->name }}</span>
                                            <span class="text-xs text-slate-400">{{ tanggal_id($reply->created_at, 'j F Y H.i') }}</span>
                                        </div>
                                        <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $reply->body }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                @include('themes.default.partials.empty-state', [
                    'title' => 'Belum ada komentar.',
                    'message' => $post->allow_comments ? 'Jadilah yang pertama berkomentar melalui formulir di bawah.' : null,
                    'iconPath' => 'M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z',
                ])
            @endforelse
        </div>

        @if($post->allow_comments)
            {{-- Form komentar --}}
            <form id="form-komentar" action="{{ route('comments.store', $post->slug) }}" method="post"
                class="mt-8 scroll-mt-24 rounded-sm border border-slate-300 bg-white p-5 shadow-sm sm:p-7">
                @csrf
                <h3 class="font-heading text-lg font-bold text-primary-900">Tulis Komentar</h3>

                <div x-cloak x-show="parentId"
                    class="mt-3 flex items-center justify-between rounded bg-primary-50 px-3 py-2 text-sm text-primary-800">
                    <span>Membalas komentar <strong x-text="parentName"></strong></span>
                    <button type="button" @click="parentId = null; parentName = ''" class="font-semibold text-primary-700 hover:text-primary-900">Batal</button>
                </div>

                <input type="hidden" name="parent_id" :value="parentId">
                {{-- Honeypot anti-spam --}}
                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                @if($errors->any())
                    <div class="mt-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="comment-name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" id="comment-name" name="name" value="{{ old('name') }}" required
                            class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
                    </div>
                    <div>
                        <label for="comment-email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="comment-email" name="email" value="{{ old('email') }}" required
                            class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">
                        <p class="mt-1 text-xs text-slate-400">Email tidak akan dipublikasikan.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="comment-body" class="mb-1.5 block text-sm font-semibold text-slate-700">Komentar <span class="text-red-500">*</span></label>
                    <textarea id="comment-body" name="body" rows="4" required
                        class="w-full rounded border border-slate-300 px-3.5 py-2.5 text-sm focus:border-primary-600 focus:outline-none focus:ring-1 focus:ring-primary-600">{{ old('body') }}</textarea>
                </div>
                <button type="submit"
                    class="mt-5 rounded-sm bg-primary-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-primary-800">
                    Kirim Komentar
                </button>
            </form>
        @elseif($comments->isNotEmpty())
            <p class="mt-6 rounded-sm border border-slate-200 bg-slate-50 px-4 py-3 text-center text-sm text-slate-500">Kolom komentar telah ditutup.</p>
        @endif
    </section>
@endif
