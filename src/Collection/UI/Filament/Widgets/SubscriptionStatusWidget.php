<?php

// src/Collection/UI/Filament/Widgets/SubscriptionStatusWidget.php

namespace Numista\Collection\UI\Filament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class SubscriptionStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.subscription-status-widget';

    // This property will hold all the data for our view.
    public ?array $subscriptionData = [];

    /**
     * The mount method is called when the widget is initialized.
     * This is the perfect place to prepare our data.
     */
    public function mount(): void
    {
        $tenant = Filament::getTenant();

        // If for some reason there is no tenant, do nothing.
        if (! $tenant) {
            $this->subscriptionData = null;

            return;
        }

        $status = $tenant->subscription_status;
        $endDate = $tenant->subscription_ends_at;

        // Use a match expression to cleanly determine the widget's content.
        $this->subscriptionData = match ($status) {
            'active' => [
                'icon' => 'heroicon-o-check-circle',
                'color' => 'success',
                'title' => $endDate
                    ? __('panel.widget_subscription_title_cancels_on')
                    : __('panel.widget_subscription_title_active'),
                'description' => $endDate
                    ? __('panel.widget_subscription_desc_cancels_on', ['date' => $endDate->format('d/m/Y')])
                    : __('panel.widget_subscription_desc_active', ['date' => $endDate?->format('d/m/Y') ?? 'N/A']),
                'button_text' => $endDate
                    ? __('panel.widget_subscription_button_reactivate')
                    : __('panel.widget_subscription_button_manage'),
                'show_button' => true,
            ],
            'inactive', 'past_due' => [
                'icon' => 'heroicon-o-exclamation-circle',
                'color' => 'danger',
                'title' => __('panel.widget_subscription_title_inactive'),
                'description' => __('panel.widget_subscription_desc_inactive'),
                'button_text' => __('panel.widget_subscription_button_renew'),
                'show_button' => true,
            ],
            'canceled' => [
                'icon' => 'heroicon-o-x-circle',
                'color' => 'gray',
                'title' => __('panel.widget_subscription_title_canceled'),
                'description' => __('panel.widget_subscription_desc_canceled'),
                'button_text' => __('panel.widget_subscription_button_resubscribe'),
                'show_button' => true, // You might want to link them to the subscribe page
            ],
            default => [
                'icon' => 'heroicon-o-credit-card',
                'color' => 'warning',
                'title' => __('panel.widget_subscription_title_none'),
                'description' => __('panel.widget_subscription_desc_none'),
                'button_text' => __('panel.widget_subscription_button_subscribe'),
                'show_button' => true,
            ],
        };
    }
}
