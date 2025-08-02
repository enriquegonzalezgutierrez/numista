@extends('layouts.public')

@section('title', __('public.checkout'))

@push('head-scripts')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">{{ __('public.checkout') }}</h1>
    
    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative" role="alert">
            <strong class="font-bold">{{ __('Whoops! Something went wrong.') }}</strong>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form id="payment-form" action="{{ route('checkout.store') }}" method="POST"
          x-data="checkoutForm('{{ $stripeKey }}', '{{ $clientSecret }}')">
        @csrf
        <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start" x-data="{ addressOption: '{{ $addresses->isNotEmpty() ? 'existing' : 'new' }}' }">
            <section class="lg:col-span-7 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('public.shipping_information') }}</h2>
                <div class="mt-4">
                    @include('public.checkout.partials.address-selection')
                </div>
            </section>

            <section class="lg:col-span-5 mt-8 lg:mt-0 rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm h-fit sticky top-8">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('public.order_summary') }}</h2>
                @include('public.checkout.partials.order-summary')
                
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('public.payment_details') }}</h2>
                    
                    <div id="payment-element" class="mt-4"></div>
                    <div id="payment-message" class="hidden mt-2 text-sm text-red-600 dark:text-red-400"></div>

                    <div class="mt-6">
                        {{-- THE FIX: Changed the id from "submit" to "submit-button" --}}
                        <button id="submit-button" 
                                @click.prevent="handleSubmit" 
                                :disabled="processing"
                                class="w-full flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!processing">{{ __('public.pay_amount', ['amount' => number_format($total, 2, ',', '.') . ' €']) }}</span>
                            <span x-show="processing" class="italic">{{ __('public.processing') }}</span>
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('checkoutForm', (stripeKey, clientSecret) => ({
            stripe: null,
            elements: null,
            processing: false,

            init() {
                if (typeof Stripe === 'undefined') {
                    console.error('Stripe.js has not loaded. Cannot initialize payment form.');
                    return;
                }
                
                this.stripe = Stripe(stripeKey);

                this.elements = this.stripe.elements({
                    clientSecret: clientSecret,
                    appearance: { 
                        theme: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'night' : 'stripe',
                        labels: 'floating' 
                    },
                });
                
                const paymentElement = this.elements.create('payment');
                paymentElement.mount('#payment-element');
            },

            async handleSubmit() {
                if (this.processing || !this.stripe || !this.elements) {
                    return;
                }
                
                this.processing = true;

                const form = document.getElementById('payment-form');

                const { error } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    redirect: 'if_required'
                });

                if (error) {
                    if (error.type === "card_error" || error.type === "validation_error") {
                        this.showMessage(error.message);
                    } else {
                        this.showMessage(@json(__('public.payment_error_unexpected')));
                    }
                    this.processing = false;
                } else {
                    // This will now call the native form submit function
                    form.submit();
                }
            },

            showMessage(messageText) {
                const messageContainer = document.querySelector("#payment-message");
                messageContainer.classList.remove("hidden");
                messageContainer.textContent = messageText;
                setTimeout(() => {
                    messageContainer.classList.add("hidden");
                    messageContainer.textContent = "";
                }, 5000);
            }
        }));
    });
</script>
@endsection