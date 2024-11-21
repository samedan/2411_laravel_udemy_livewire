<div x-data="{ isOpen: false }">
    {{-- Button OPEN Overlay --}}
    <button x-on:click="isOpen = true; 
      setTimeout(()=> document.querySelector('#live-search-field').focus(),50) " 
      style="background:none; border:none; padding:0; margin:0; outline:none; cursor:pointer" href="#" class="text-white mr-2 header-search-icon" title="Search" data-toggle="tooltip" data-placement="bottom"><i class="fas fa-search"></i></button>

    <div class="search-overlay " x-bind:class="isOpen ? 'search-overlay--visible' : '' ">
        <div class="search-overlay-top shadow-sm">
          <div class="container container--narrow">
            <label for="live-search-field" class="search-overlay-icon"><i class="fas fa-search"></i></label>
            <input wire:model.live.debounce.750ms="searchTerm" autocomplete="off" type="text" id="live-search-field" class="live-search-field" placeholder="What are you interested in?">
            {{-- Button CLOSE Overlay --}}
            <span x-on:click="isOpen=false" class="close-live-search"><i class="fas fa-times-circle"></i></span>
          </div>
        </div>
    
        <div class="search-overlay-bottom">
          <div class="container container--narrow py-3">
            <div class="circle-loader"></div>
            <div class="live-search-results live-serach-results--visible">

              <div class="list-group shadow-sm">
                <div class="list-group-item active"><strong>Search Results</strong> 
                  ({{count($results)}} {{count($results) > 1 ? "results": "result"}} found )
                </div>
                  @foreach ($results as $post)
                  <a href="/post/{{$post->id}}$" class="list-group-item list-group-item-action">
                    <img class="avatar-tiny" src="{{$post->user->avatar}}"> 
                      <strong>{{$post->title}}</strong>
                    <span class="text-muted small">by {{$post->user->username}} on {{$post->created_at->format('n/j/Y')}}${
                              postDate.getMonth() + 1
                          }/${postDate.getDate()}/${postDate.getFullYear()}</span>
                  </a>
                      
                  @endforeach
            </div>
          </div>
        </div>
      </div>
</div>
 