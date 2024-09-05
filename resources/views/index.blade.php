@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                
                <form id="metrics-form" class="card my-4">
                    <div class="card-header">
                        <h4 class="card-title">Check PageSpeed Metrics</h4>
                        <div id="loadingBtn" class="spinner-border text-primary ms-auto d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-5">
                            <div class="col-md-4">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">URL</label>
                                    <input id="url" name="url" type="text" class="form-control" name="example-text-input" placeholder="http://example.com" pattern="https?://.+" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Strategy</label>
                                <select name="strategy" id="strategy"  class="form-select">
                                    @foreach ($strategies as $strategy)
                                        <option value="{{ $strategy->name }}">{{ $strategy->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Categories</label>
                                    <div>
                                        @foreach ($categories as $category)
                                            <label class="form-check">
                                                <input type="checkbox" class="form-check-input" name="categories[]" value="{{ $category->name }}">{{ str_replace('_', ' ', $category->name) }}
                                            </label>
                                        @endforeach
                                    </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        
                        <button type="submit" class="btn btn-primary">Get Metrics</button>
                    </div>
                </form>
            </div>
            <div class="col-12">                
                <div id="metrics-results" class="row row-cards"></div>
                <button type="button" id="saveMetrics" style="display:none;" class="btn btn-success ms-auto">Save Metric Run</button>
            </div>
            <div id="successMessage" class="col-md-6 offset-md-3 mt-4 d-none">
                <!-- <div class="alert alert-success" role="alert">
                    <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                    </div>
                    <div>
                        Metric Saved
                    </div>
                    </div>
                </div> -->
                <div class="alert alert-important alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                    <div>
                        <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                    </div>
                    <div>
                        Metric Saved
                    </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>
            <div id="errorMessage" class="col-md-6 offset-md-3 mt-4 d-none">
                <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                        </div>
                        <div>
                            Please provide a URL and select at least one category.
                        </div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>                
        </div>        
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('metrics-form');
        const saveMetricsButton = document.getElementById('saveMetrics');
        const resultsDiv = document.getElementById('metrics-results');
        const loadingBtn = document.getElementById('loadingBtn');
        const currentMetrics = []
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            loadingBtn.classList.remove('d-none');
            const formData = new FormData(form);
            
            const requestOptions = {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData,
            };

            fetch("{{ route('get.pagespeed.metrics') }}", requestOptions)
                .then(response => response.json())
                .then(data => {
                    currentMetrics.splice(0);
                    const categories = data.lighthouseResult.categories;

                    for (const category in categories) {
                        const cardContainer = document.createElement('div');
                        cardContainer.classList.add('col-lg-3', 'col-md-6');

                        const card = document.createElement('div');
                        card.classList.add('card', 'my-4');

                        const cardBody = document.createElement('div');
                        cardBody.classList.add('card-body','text-center');

                        const title = document.createElement('h5');
                        title.classList.add('card-title');
                        title.textContent = categories[category].title;

                        const score = document.createElement('p');
                        score.classList.add('card-text','display-5');
                        score.textContent = `${categories[category].score}`;

                        cardBody.appendChild(title);
                        cardBody.appendChild(score);
                        card.appendChild(cardBody);
                        cardContainer.appendChild(card);
                        resultsDiv.appendChild(cardContainer);

                        currentMetrics.push({
                            category: categories[category].title,
                            score: categories[category].score,
                        });
                    }                        
                    saveMetricsButton.style.display = 'block';
                })
                .catch(error =>{                    
                    errorMessage.classList.remove('d-none');
                    setTimeout(() => {
                        errorMessage.classList.add('d-none');
                    }, 3000);
                })
                .finally(() => loadingBtn.classList.add('d-none'))                    
            });

        saveMetricsButton.addEventListener('click', function() {
            const formData = new FormData(form);
            formData.append('currentMetrics', JSON.stringify(currentMetrics));
            
            const requestOptions = {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData,
            };

             fetch("{{ route('post.pagespeed.metrics') }}", requestOptions)
             .then(response => response.json())
             .then(() => {
                successMessage.classList.remove('d-none');
                setTimeout(() => {
                    successMessage.classList.add('d-none');
                }, 3000);
             })
             .catch(error => console.error('Error:', error));
        });
    });
</script>