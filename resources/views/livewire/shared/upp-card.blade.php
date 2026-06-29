<div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    <div class="px-6 pt-6 pb-4">
        <flux:heading size="lg">UPP</flux:heading>
    </div>
    @foreach([['strength','Strength'],['dexterity','Dexterity'],['endurance','Endurance'],['intelligence','Intelligence'],['education','Education'],['socialStanding','Social Standing']] as [$field, $label])
        @php $val = $this->$field; $dm = \App\Support\Mgt2::dm($val); @endphp
        <div class="flex items-center gap-3 px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }}">
            <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">{{ $label }}</span>
            <select wire:model.live="{{ $field }}" class="w-16 shrink-0 cursor-pointer rounded-lg border border-zinc-200 border-b-zinc-300/80 bg-white py-1.5 pl-2 pr-6 text-right text-sm text-zinc-700 shadow-xs dark:border-white/10 dark:bg-zinc-700 dark:text-zinc-200">
                @foreach($statOptions as $value => $hex)
                    <option value="{{ $value }}">{{ $hex }}</option>
                @endforeach
            </select>
            <span class="w-12 shrink-0 text-right text-xs {{ $dm > 0 ? 'text-green-600' : ($dm < 0 ? 'text-red-500' : 'text-zinc-400') }}">
                DM {{ $dm >= 0 ? '+'.$dm : $dm }}
            </span>
        </div>
    @endforeach
    <div class="pb-2"></div>
</div>
