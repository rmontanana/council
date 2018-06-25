@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                   <avatar-form :user="{{ $profileUser }}"></avatar-form>
                </div>
                @foreach ($activities as $date => $activity)
                    <h3 class="page-header">
                        {{ $date }}
                    </h3>
                    @forelse($activity as $record)
                        @if (view()->exists("profiles.activities.{$record->type}"))
                            @include("profiles.activities.{$record->type}", ['activity' => $record])
                        @endif
                    @empty
                        <p>There is no activity for this user yet!</p>
                    @endforelse
                @endforeach
                {{--{{ $threads->links() }}--}}
            </div>
        </div>
    </div>
@endsection