<ol class="dd-list">
    @forelse ($menuItems as $item)
        <li class="dd-item" data-id="{{ $item->id }}">
            <div class="pull-right item_action">
                @if (permission('menu-module-delete'))
                <button type="button" class="btn btn-danger btn-sm float-right" 
                onclick="deleteItem('{{ $item->id }}')">
                    <i class="fas fa-trash"></i>
                </button>
                <form action="{{ route('menu.module.delete',['module'=>$item->id]) }}" id="delete_form_{{ $item->id }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endif
                
                @if (permission('menu-module-edit'))
                <a href="{{ route('menu.module.edit',['menu'=>$item->menu_id,'module'=>$item->id]) }}" 
                    class="btn btn-primary btn-sm float-right edit"><i class="fas fa-edit"></i>
                </a>
                @endif
            </div>
            <div class="dd-handle">
                @if ($item->type == 1)
                    <strong>Divider: {{ $item->divider_title }}</strong>
                @else
                    <span>{{ $item->module_name }}</span> <small class="url">{{ $item->url }}</small>
                @endif
            </div>
            @if (!$item->children->isEmpty())
                <x-menu-builder :menuItems="$item->children"/>
            @endif
        </li>
    @empty
        <div class="text-center">
            <strong>No menu item found</strong>
        </div>
    @endforelse
</ol>