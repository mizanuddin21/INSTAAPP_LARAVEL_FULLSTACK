@extends('layout.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">

    <div class="col-md6 mb-3">
        <!-- Toggle Button to add new post -->
        <button id="toggle-post-form" class="btn btn-light mb-3" style="display: flex; align-items: center; gap: 8px;">
            <img src="{{ asset('new-post.png') }}" alt="New Post" width="24" height="24" />
            New Post
        </button>

        <div id="post-form-container" class="card p-3 mb-4 d-none">
            <div class="card p-3 mb-4">
                <h5>Create a New Post</h5>
                <form id="create-post-form" method="POST" enctype="multipart/form-data" action="javascript:void(0);">
                    @csrf
                    <div class="mb-3">
                        <label for="caption" class="form-label">Caption</label>
                        <input type="text" class="form-control" id="caption" name="caption" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>

                <div id="post-success" class="text-success mt-2 d-none">Post created successfully!</div>
                <div id="post-error" class="text-danger mt-2 d-none">Something went wrong.</div>
            </div>
        </div>
    </div>

        @foreach($posts as $post)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <!-- Post header -->
                    <div class="card-header d-flex align-items-center">
                        <div class="me-2">
                            <img src="{{ asset('default-avatar.png') }}" alt="avatar" class="rounded-circle" width="40">
                        </div>
                        <div>
                            <strong>User {{ $post->user_id }}</strong>
                            <div class="text-muted small">{{ $post->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <!-- Post image -->
                    @if($post->image_url)
                        <img src="{{ $post->image_url }}" class="post-image" alt="Post Image">
                    @endif

                    <!-- Post caption -->
                    <div class="card-body" style="margin-left: -0.5rem;">
                        <p>{{ $post->caption }}</p>
                    </div>

                    <!-- Comment Section -->
                    <div>
                        <!-- Toggle button, with some bottom margin -->
                        <button class="btn btn-link btn-sm ms-2 mb-2 p-0" onclick="toggleComments({{ $post->id }})">
                            Show/Hide Comments ({{ $post->comments->count() }})
                        </button>

                        <!-- Comments container, hidden by default -->
                        <div id="comments-list-{{ $post->id }}" class="d-none">
                            @if($post->comments->count())
                                <hr>
                                <h6 class="text-muted ms-2">Comments ({{ $post->comments->count() }}):</h6>
                                <div class="ms-3">
                                    <ul class="list-unstyled">
                                        @foreach($post->comments as $comment)
                                            <li class="mb-2">
                                                <strong>{{ $comment->user->name ?? 'Unknown' }}:</strong> {{ $comment->content }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Post actions -->
                    <div class="d-flex align-items-center gap-3 mb-3 ms-2">
                        <!-- Like Button -->
                        <form onsubmit="likePost(event, {{ $post->id }})" class="m-0 p-0">
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0 like-button-{{ $post->id }}">
                                @if($post->is_liked_by_auth_user)
                                    <span style="color: hotpink;">❤️ Like</span>
                                @else
                                    <span>🤍 Like</span>
                                @endif
                            </button>
                        </form>

                        <!-- Hidden comment form after click the button -->
                        <div id="comment-box-{{ $post->id }}" class="mt-2 d-none">
                            <form onsubmit="postComment(event, {{ $post->id }})">
                                <textarea id="comment-input-{{ $post->id }}" class="form-control mb-2" placeholder="Write a comment..."></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                            </form>
                        </div>

                        <!-- Comment Toggle Button -->
                        <button class="btn btn-link btn-sm text-decoration-none p-0"
                                onclick="toggleCommentBox({{ $post->id }})">
                            💬 Comment
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggle-post-form');
        const formContainer = document.getElementById('post-form-container');

        if (!toggleBtn || !formContainer) {
            console.warn('Toggle button or form container not found!');
            return;
        }

        toggleBtn.addEventListener('click', () => {
            formContainer.classList.toggle('d-none');
        });
    });
    
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('create-post-form');
        const successMsg = document.getElementById('post-success');
        const errorMsg = document.getElementById('post-error');

        const accessToken = "{{ $token ?? '' }}"; // Laravel Blade variable (make sure it's passed)

        if (!form) {
            console.warn('Form with ID "create-post-form" not found.');
            return;
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const response = await fetch('http://127.0.0.1:8000/api/posts', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': `Bearer ${accessToken}`,
                        'Accept': 'application/json'
                    },
                    credentials: 'include' // Ensures cookies are sent (important if using Sanctum)
                });

                if (!response.ok) {
                    const err = await response.text();
                    throw new Error(`HTTP ${response.status} - ${err}`);
                }

                const data = await response.json();
                console.log('Post created:', data);

                window.location.href = data.redirect;

                // Show success
                successMsg?.classList.remove('d-none');
                errorMsg?.classList.add('d-none');

                // Reset form
                form.reset();

            } catch (error) {
                console.error('Failed to create post:', error);

                // Show error
                errorMsg?.classList.remove('d-none');
                successMsg?.classList.add('d-none');
            }
        });
    });


    function postComment(event, postId) {
        event.preventDefault();

        const input = document.getElementById(`comment-input-${postId}`);
        const content = input.value.trim();
        const accessToken = "{{ $token }}";

        if (!content) return;

        fetch(`/api/posts/${postId}/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json'
            },
            credentials: 'same-origin', // <== This is important
            body: JSON.stringify({ content: content })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload(); // or dynamically add comment to the list
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error(error);
            alert('Failed to post comment');
        });
    }

    function likePost(event, postId) {
        event.preventDefault();

        const accessToken = "{{ $token }}";

        fetch(`/api/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const btn = document.querySelector(`.like-button-${postId}`);
        
            if (btn) {
                // Use same format as Blade
                if (data.liked) {
                    btn.innerHTML = `<span style="color: hotpink;">❤️ Like</span>`;
                } else {
                    btn.innerHTML = `<span>🤍 Like</span>`;
                }
            }
        })
        .catch(error => {
            console.error('Like failed:', error);
            alert('Failed to like the post');
        });
    }

    function toggleCommentBox(postId) {
        const box = document.getElementById(`comment-box-${postId}`);
        box.classList.toggle('d-none');
    }

    function toggleComments(postId) {
        const commentsDiv = document.getElementById(`comments-list-${postId}`);
        commentsDiv.classList.toggle('d-none');
    }
</script>