<div>
    <div>
        <x-filament::section :heading="trans('tomato.otp')" :description="trans('tomato.otp_description')">
            <x-filament-panels::form wire:submit.prevent="authenticate">
                {{ $this->form }}

                {{ $this->submitAction }}
            </x-filament-panels::form>
        </x-filament::section>

        <div class="text-center my-4">
            <span class="text-gray-400">Don't get the code? please <span class="underline text-primary-500">{{ $this->getResendAction }}</span></span>
            <x-filament-actions::modals />
        </div>
    </div>
</div>
