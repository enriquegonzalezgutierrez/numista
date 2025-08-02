<?php

// lang/es/mail.php

return [
    // --- General ---
    'hello' => '¡Hola!',
    'regards' => 'Saludos',
    'whoops' => '¡Vaya!',
    'all_rights_reserved' => 'Todos los derechos reservados.',
    'subcopy' => 'Si tienes problemas haciendo clic en el botón ":actionText", copia y pega la siguiente URL en tu navegador:',
    'password_reset_subject' => 'Notificación de Restablecimiento de Clave de Acceso',
    'password_reset_line_1' => 'Recibes este correo porque hemos recibido una solicitud de restablecimiento de clave de acceso para tu cuenta.',
    'password_reset_action' => 'Restablecer Clave de Acceso',
    'password_reset_expire' => 'Este enlace de restablecimiento expirará en :count minutos.',
    'password_reset_line_2' => 'Si no solicitaste un restablecimiento, no se requiere ninguna acción adicional.',

    // --- Contact Seller Email ---
    'contact_subject' => 'Consulta sobre tu artículo: :itemName',
    'contact_title' => 'Nueva consulta sobre tu artículo: ":itemName"',
    'contact_intro' => 'Has recibido un nuevo mensaje de un posible comprador.',
    'contact_from' => 'De',
    'contact_email' => 'Email',
    'contact_message' => 'Mensaje',
    'contact_view_item' => 'Ver Artículo',
    'contact_thanks' => 'Gracias,',

    // --- Order Confirmation Email (for Customer) ---
    'order_confirmation_subject' => 'Confirmación de tu pedido #:orderNumber',
    'order_confirmation_title' => '¡Gracias por tu pedido!',
    'order_confirmation_intro' => 'Hemos recibido tu pedido **#:orderNumber** y ya lo estamos preparando.',
    'order_summary' => 'Resumen del Pedido',
    'product' => 'Producto',
    'quantity' => 'Cantidad',
    'price' => 'Precio',
    'total' => 'Total',
    'view_order' => 'Ver Mi Pedido',
    'order_confirmation_cta' => 'Puedes ver los detalles completos de tu pedido en tu cuenta.',

    // --- Seller New Order Notification ---
    'seller_notification_subject' => '¡Nuevo Pedido Recibido! #:orderNumber',
    'seller_notification_title' => 'Has Recibido un Nuevo Pedido',
    'seller_notification_intro' => '¡Buenas noticias! Has recibido un nuevo pedido en :appName.',
    'seller_notification_order_details' => 'Detalles del Pedido',
    'seller_notification_order_number' => 'Número de Pedido',
    'seller_notification_customer' => 'Cliente',
    'seller_notification_date' => 'Fecha',
    'seller_notification_items_sold' => 'Artículos Vendidos',
    'seller_notification_total_order' => 'Total del Pedido',
    'seller_notification_shipping_address' => 'Dirección de Envío',
    'seller_notification_manage_order' => 'Puedes gestionar este pedido desde tu panel de control.',
    'seller_notification_cta' => 'Ir al Panel de Control',
    'seller_notification_team' => 'El equipo de :appName',

    // --- New Tenant Welcome Email ---
    'welcome_subject' => '¡Bienvenido a :appName!',
    'welcome_title' => '¡Bienvenido a :appName, :userName!',
    'welcome_intro' => 'Gracias por registrar tu colección con nosotros.',
    'welcome_next_step' => 'Tu cuenta ha sido creada y estás a un solo paso de empezar. El siguiente paso es elegir un plan de suscripción para activar tu panel de administración.',
    'welcome_ignore' => 'Si ya has completado el pago, ¡puedes ignorar este mensaje y empezar a disfrutar de la plataforma!',
    'welcome_cta' => 'Ir a mi Panel de Control',

    // --- Subscription Lifecycle Emails ---
    'subscription_activated_subject' => '¡Tu suscripción está activa!',
    'subscription_activated_title' => '¡Suscripción Activada!',
    'subscription_activated_body' => 'Tu suscripción para la colección ":tenantName" ha sido activada con éxito. ¡Ya puedes empezar a añadir tus ítems!',

    'subscription_renewed_subject' => 'Tu suscripción ha sido renovada',
    'subscription_renewed_title' => '¡Suscripción Renovada!',
    'subscription_renewed_body' => 'Hola :userName, te confirmamos que tu suscripción para la colección ":tenantName" ha sido renovada con éxito. ¡Gracias por continuar con nosotros!',

    'subscription_payment_failed_subject' => 'Acción Requerida: Fallo en el pago de tu suscripción',
    'subscription_payment_failed_title' => 'Fallo en el Pago',
    'subscription_payment_failed_body' => 'Hola :userName, no hemos podido procesar el pago para renovar tu suscripción de la colección ":tenantName". Para evitar la interrupción del servicio, por favor, actualiza tu método de pago.',
    'subscription_payment_failed_cta' => 'Actualizar Método de Pago',

    'subscription_cancellation_subject' => 'Confirmación de la cancelación de tu suscripción',
    'subscription_cancellation_title' => 'Tu Suscripción Será Cancelada',
    'subscription_cancellation_body' => 'Hola :userName, te confirmamos que tu suscripción para la colección ":tenantName" ha sido programada para cancelarse. Tu acceso permanecerá activo hasta el final de tu periodo de facturación actual, el **:endDate**.',
    'subscription_cancellation_cta' => 'Reactivar Suscripción',

    'subscription_reactivated_subject' => 'Tu suscripción ha sido reactivada',
    'subscription_reactivated_title' => '¡Tu Suscripción Vuelve a Estar Activa!',
    'subscription_reactivated_body' => 'Hola :userName, te confirmamos que has reactivado tu suscripción para la colección ":tenantName". Se renovará automáticamente en la próxima fecha de facturación. ¡Nos alegra que te quedes!',
];
