@extends('layouts.app')

@section('title', 'Generation Status')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Generation Status</h1>
            <a href="{{ route('generations.create') }}" class="btn btn-outline-primary">New Generation</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <span id="status-badge" class="badge bg-secondary ms-2">{{ ucfirst($generation->status) }}</span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>Created:</strong> {{ $generation->created_at->format('M d, Y H:i') }}
                    </div>
                </div>

                @if ($generation->model_used)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Model:</strong> {{ $generation->model_used }}
                        </div>
                        @if ($generation->tokens_used)
                            <div class="col-md-6 text-md-end">
                                <strong>Tokens:</strong> {{ $generation->tokens_used }}
                            </div>
                        @endif
                    </div>
                @endif

                <hr>

                <div id="processing-section" class="text-center py-4" style="display: {{ $generation->isPending() || $generation->isProcessing() ? 'block' : 'none' }};">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Generating your recipe...</h5>
                    <p class="text-muted">This may take up to a minute. Please don't close this page.</p>
                </div>

                <div id="completed-section" style="display: {{ $generation->isCompleted() ? 'block' : 'none' }};">
                    <div class="alert alert-success">
                        <strong>Recipe generated successfully!</strong>
                    </div>

                    @if ($recipe = $generation->getRecette())
                        <div class="card bg-light">
                            <div class="card-body">
                                <h4>{{ $recipe->title }}</h4>
                                <p class="text-muted mb-3">{{ $recipe->description }}</p>

                                <div class="row mb-3">
                                    <div class="col-4 text-center">
                                        <div class="text-muted small">Prep Time</div>
                                        <strong>{{ $recipe->prep_time }} min</strong>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="text-muted small">Cook Time</div>
                                        <strong>{{ $recipe->cook_time }} min</strong>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="text-muted small">Servings</div>
                                        <strong>{{ $recipe->servings }}</strong>
                                    </div>
                                </div>

                                @if ($recipe->categories->count())
                                    <div class="mb-3">
                                        @foreach ($recipe->categories as $category)
                                            <span class="badge bg-primary">{{ $category->name }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-primary">View Full Recipe</a>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Recipe data not available.</p>
                    @endif
                </div>

                <div id="failed-section" class="text-center py-4" style="display: {{ $generation->isFailed() ? 'block' : 'none' }};">
                    <div class="alert alert-danger">
                        <strong>Generation failed</strong>
                        @if ($generation->error_message)
                            <p class="mb-0 mt-2">{{ $generation->error_message }}</p>
                        @endif
                    </div>
                    <a href="{{ route('generations.create') }}" class="btn btn-primary">Try Again</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generationId = {{ $generation->id }};
        const currentStatus = '{{ $generation->status }}';

        if (currentStatus === 'completed' || currentStatus === 'failed') {
            return;
        }

        const statusBadge = document.getElementById('status-badge');
        const processingSection = document.getElementById('processing-section');
        const completedSection = document.getElementById('completed-section');
        const failedSection = document.getElementById('failed-section');

        const pollInterval = setInterval(async function() {
            try {
                const response = await fetch(`/generations/${generationId}`, {
                    headers: {
                        'Accept': 'text/html',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    return;
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newBadge = doc.getElementById('status-badge');
                if (newBadge) {
                    statusBadge.textContent = newBadge.textContent;
                    statusBadge.className = newBadge.className;
                }

                const newStatus = newBadge?.textContent?.trim().toLowerCase();

                if (newStatus === 'completed') {
                    clearInterval(pollInterval);
                    processingSection.style.display = 'none';
                    completedSection.style.display = 'block';
                    failedSection.style.display = 'none';

                    const newCompletedSection = doc.getElementById('completed-section');
                    if (newCompletedSection) {
                        completedSection.innerHTML = newCompletedSection.innerHTML;
                    }
                } else if (newStatus === 'failed') {
                    clearInterval(pollInterval);
                    processingSection.style.display = 'none';
                    completedSection.style.display = 'none';
                    failedSection.style.display = 'block';

                    const newFailedSection = doc.getElementById('failed-section');
                    if (newFailedSection) {
                        failedSection.innerHTML = newFailedSection.innerHTML;
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 3000);

        window.addEventListener('beforeunload', function() {
            clearInterval(pollInterval);
        });
    });
</script>
@endsection
