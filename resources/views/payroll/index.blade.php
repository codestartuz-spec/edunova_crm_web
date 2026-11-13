@extends('layouts.app')
@section('title', 'Ish haqi')

@section('content')
    <x-page-header icon="briefcase" title="Hodimlar" subtitle="O'qituvchi va hodimlarga to'lovlar" />
    @include('staff._tabs', ['active' => $tab])

    <div class="card overflow-hidden">
        <div class="table-wrap">
            <table class="table table-anim">
                <thead><tr><th>F.I.O</th><th>Lavozim</th>@if ($tab === 'teachers')<th class="text-right">To'lanmagan qoldiq</th>@endif<th class="text-right">Shu oy to'landi</th></tr></thead>
                <tbody>
                @forelse ($rows as $r)
                    <tr data-href="{{ route('payroll.show', $r['user']) }}">
                        <td class="font-medium text-ink-900 dark:text-white"><a href="{{ route('payroll.show', $r['user']) }}" class="hover:text-brand-600">{{ $r['user']->name }}</a></td>
                        <td><span class="{{ $r['user']->role->badgeClass() }}">{{ $r['user']->role->label() }}</span></td>
                        @if ($tab === 'teachers')<td class="text-right font-semibold {{ $r['remaining'] > 0 ? 'text-brand-600' : '' }}"><span data-countup="{{ (int) $r['remaining'] }}" data-unit="money">{{ \App\Support\Format::money($r['remaining']) }}</span></td>@endif
                        <td class="text-right"><span data-countup="{{ (int) $r['paid_month'] }}" data-unit="money">{{ \App\Support\Format::money($r['paid_month']) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-10 text-center text-ink-500">Ro'yxat bo'sh.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
