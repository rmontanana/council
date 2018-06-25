{{--Editing the thread--}}
<div class="panel panel-default" v-if="editing">
    <div class="panel-heading">
        <div class="level">
            <input type="text" class="form-control" v-model="form.title"/>
        </div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <wysiwyg v-model="form.body"></wysiwyg>
        </div>
    </div>
    <div class="panel-footer">
        <div class="level">
            <button class="btn btn-xs level-item" @click="editing = true" v-show="! editing">Edit</button>
            <button class="btn btn-xs btn-primary level-item" @click="update" v-show="editing">Update</button>
            <button class="btn btn-xs level-item" @click="resetForm">Cancel</button>
            @can('update', $thread)
                <form method="POST" action="{{ $thread->path() }}" class="ml-a">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                </form>
            @endcan
        </div>
    </div>
</div>
{{--NOT Editing the thread--}}
<div class="panel panel-default" v-else>
    <div class="panel-heading">
        <div class="level">
            <span class="flex">
                <img src="{{ asset($thread->creator->avatar_path) }}" class="mr" width="25" height="25" alt="{{ $thread->creator->name}}">
                <a href="{{ route('profile', $thread->creator->name) }}">{{ $thread->creator->name }}</a> posted
                <span v-text="form.title"></span>
            </span>
        </div>
    </div>
    <div class="panel-body" v-html="form.body">
    </div>
    <div class="panel-footer" v-if="authorize('owns', this.thread)">
        <button class="btn btn-xs" @click="editing = true">Edit</button>
    </div>
</div>