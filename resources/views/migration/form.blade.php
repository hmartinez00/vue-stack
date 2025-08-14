<div class="space-y-6">
    
    <div>
        <x-input-label for="migration" :value="__('Migration')"/>
        <x-text-input id="migration" name="migration" type="text" class="mt-1 block w-full" :value="old('migration', $migration?->migration)" autocomplete="migration" placeholder="Migration"/>
        <x-input-error class="mt-2" :messages="$errors->get('migration')"/>
    </div>
    <div>
        <x-input-label for="batch" :value="__('Batch')"/>
        <x-text-input id="batch" name="batch" type="text" class="mt-1 block w-full" :value="old('batch', $migration?->batch)" autocomplete="batch" placeholder="Batch"/>
        <x-input-error class="mt-2" :messages="$errors->get('batch')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>