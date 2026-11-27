@extends('layouts.app')

@section('title', 'إدارة الأدوار والصلاحيات')

@section('content')
<div class="space-y-6">
    <div class="bg-unicef-dark text-white p-6 border-2 border-unicef-dark">
        <h1 class="text-xl font-black">الأدوار والصلاحيات</h1>
        <p class="text-xs text-neutral-300 mt-1">أنشئ أدواراً جديدة ووزّع الصلاحيات عليها.</p>
    </div>

    @if(session('success'))
        <div class="bg-unicef-green text-white border-2 border-unicef-dark px-4 py-3 text-xs font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-600 text-white border-2 border-unicef-dark px-4 py-3 text-xs font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        @foreach($roles as $role)
        <div class="border-2 border-unicef-dark bg-white">
            <div class="bg-unicef-light border-b-2 border-unicef-dark px-4 py-3 flex justify-between items-center">
                <div>
                    <span class="font-black text-sm">{{ $role->name }}</span>
                    <code class="text-[10px] font-mono bg-unicef-dark text-white px-1.5 py-0.5 ms-2">{{ $role->slug }}</code>
                    @if($role->is_builtin)
                        <span class="text-[10px] border border-unicef-dark px-1.5 py-0.5 ms-1">مدمج</span>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="p-4 space-y-3">
                @csrf @method('PUT')

                <input name="name" value="{{ $role->name }}" required
                       class="w-full border-2 border-unicef-dark px-2 py-2 text-xs" placeholder="اسم الدور">

                @foreach($permissions as $group => $perms)
                <fieldset class="border border-unicef-dark p-2">
                    <legend class="text-[10px] font-black px-1">{{ $group }}</legend>
                    <div class="grid grid-cols-2 gap-1 mt-1">
                        @foreach($perms as $p)
                        <label class="flex items-center gap-1.5 text-[11px]">
                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                   class="accent-unicef-blue w-3.5 h-3.5"
                                   @checked($role->permissions->contains('id', $p->id))>
                            {{ $p->label }}
                        </label>
                        @endforeach
                    </div>
                </fieldset>
                @endforeach

                <div class="flex gap-2">
                    <button class="bg-unicef-blue text-white border-2 border-unicef-dark px-4 py-2 text-xs font-bold flex-1">
                        حفظ الصلاحيات
                    </button>
                </div>
            </form>

            @if(!$role->is_builtin)
            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="border-t-2 border-unicef-dark p-3"
                  onsubmit="return confirm('حذف الدور «{{ $role->name }}» نهائياً؟')">
                @csrf @method('DELETE')
                <button class="w-full bg-red-600 text-white border-2 border-unicef-dark px-4 py-2 text-xs font-bold">
                    حذف الدور
                </button>
            </form>
            @endif
        </div>
        @endforeach

        {{-- نموذج إنشاء دور جديد --}}
        <div class="border-2 border-unicef-dark bg-white">
            <div class="bg-unicef-yellow border-b-2 border-unicef-dark px-4 py-3 font-black text-sm">
                + إنشاء دور جديد
            </div>
            <form action="{{ route('admin.roles.store') }}" method="POST" class="p-4 space-y-3">
                @csrf
                <input name="name" required placeholder="الاسم (عربي)"
                       class="w-full border-2 border-unicef-dark px-2 py-2 text-xs">
                <input name="slug" required placeholder="المعرّف (إنجليزي: reviewer)"
                       pattern="[a-z0-9_\-]+"
                       class="w-full border-2 border-unicef-dark px-2 py-2 text-xs font-mono">
                <textarea name="description" placeholder="وصف اختياري"
                          class="w-full border-2 border-unicef-dark px-2 py-2 text-xs"></textarea>

                @foreach($permissions as $group => $perms)
                <fieldset class="border border-unicef-dark p-2">
                    <legend class="text-[10px] font-black px-1">{{ $group }}</legend>
                    <div class="grid grid-cols-2 gap-1 mt-1">
                        @foreach($perms as $p)
                        <label class="flex items-center gap-1.5 text-[11px]">
                            <input type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                   class="accent-unicef-blue w-3.5 h-3.5">
                            {{ $p->label }}
                        </label>
                        @endforeach
                    </div>
                </fieldset>
                @endforeach

                <button class="w-full bg-unicef-green text-white border-2 border-unicef-dark px-4 py-2 text-xs font-bold">
                    إنشاء الدور
                </button>
            </form>
        </div>
    </div>
</div>
@endsection