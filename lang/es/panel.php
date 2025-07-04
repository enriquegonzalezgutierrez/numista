<?php

// lang/es/panel.php
return [
    // Navigation & Resource Labels
    'nav_items' => 'Inventario',
    'label_item' => 'Ítem',
    'label_items' => 'Ítems',
    'nav_categories' => 'Categorías',
    'label_category' => 'Categoría',
    'label_categories' => 'Categorías',
    'nav_collections' => 'Colecciones',
    'label_collection' => 'Colección',
    'label_collections' => 'Colecciones',
    'nav_images' => 'Imágenes',
    'label_image' => 'Imagen',
    'label_images' => 'Imágenes',
    'nav_group_shop' => 'Tienda',
    'nav_orders' => 'Pedidos',
    'label_order' => 'Pedido',
    'label_orders' => 'Pedidos',
    'label_order_items' => 'Artículos del Pedido',

    // General Field & Column Labels
    'field_name' => 'Nombre',
    'field_slug' => 'Slug (URL)',
    'field_description' => 'Descripción',
    'field_image_preview' => 'Vista Previa',
    'field_alt_text' => 'Texto Alternativo',
    'field_image_file' => 'Archivo de Imagen',
    'field_parent_category' => 'Categoría Superior',
    'field_is_visible' => 'Visible al público',
    'field_items_count' => 'Nº de Ítems',
    'field_new_status' => 'Nuevo Estado',
    'field_select_categories' => 'Seleccionar categorías',
    'field_visibility' => 'Visibilidad',
    'field_collection_name' => 'Nombre de la Colección',
    'field_tenant_name' => 'Nombre de la Colección (Tenant)',
    'field_order_number' => 'Nº de Pedido',
    'field_customer' => 'Cliente',
    'field_total_amount' => 'Importe Total',
    'field_order_date' => 'Fecha del Pedido',
    'field_last_update' => 'Última Actualización',

    // General UI Text
    'placeholder_none' => 'Ninguna',
    'helper_alt_text' => 'Describe la imagen para accesibilidad y SEO.',
    'search_placeholder' => 'Buscar...',

    // Table Filters
    'filter_item_type' => 'Tipo de Ítem',
    'filter_status' => 'Estado',
    'filter_category' => 'Categoría',
    'filter_collection' => 'Colección',

    // Actions, Buttons & Modals
    'action_create' => 'Añadir',
    'action_edit' => 'Editar',
    'action_delete' => 'Eliminar',
    'action_attach' => 'Asociar',
    'action_detach' => 'Desasociar',
    'action_create_image' => 'Añadir Imagen',

    'action_bulk_change_status' => 'Cambiar estado',
    'action_bulk_attach_category' => 'Asignar categoría',
    'action_bulk_change_visibility' => 'Cambiar visibilidad',

    'modal_attach_title_category' => 'Asociar a Categorías',
    'modal_attach_title_collection' => 'Asociar a Colecciones',
    'modal_attach_button' => 'Asociar Selección',
    'modal_create_image_title' => 'Añadir Nueva Imagen',

    // Notifications
    'notification_status_updated' => 'El estado de los ítems ha sido actualizado.',
    'notification_categories_attached' => 'Las categorías han sido asignadas correctamente.',
    'notification_visibility_updated' => 'La visibilidad de las categorías ha sido actualizada.',

    // Widgets
    'widget_stats_total_items' => 'Total de Ítems',
    'widget_stats_collection_value' => 'Valor de la Colección',
    'widget_stats_items_for_sale' => 'Ítems en Venta',
    'widget_stats_categories' => 'Categorías Creadas',
    'widget_chart_items_by_type' => 'Ítems por Tipo',
    'widget_table_latest_items' => 'Últimos Ítems Añadidos',
    'widget_table_added_at' => 'Añadido el',
    'widget_table_view_action' => 'Ver / Editar',
    'widget_chart_dataset_label' => 'Ítems',
    'widget_table_column_name' => 'Nombre',
    'widget_table_column_type' => 'Tipo',
    'widget_stats_collections' => 'Colecciones Creadas',
    'widget_table_valuable_items' => 'Ítems más Valiosos en Venta',
    'widget_table_column_sale_price' => 'Precio de Venta',

    // Descriptions for Stats Widget (Add these new keys)
    'widget_stats_total_items_desc' => 'Número total de ítems en el inventario',
    'widget_stats_collection_value_desc' => 'Suma del precio de compra de todos los ítems',
    'widget_stats_items_for_sale_desc' => 'Ítems marcados actualmente para la venta',
    'widget_stats_categories_desc' => 'Total de categorías para organizar ítems',
    'widget_stats_collections_desc' => 'Agrupaciones temáticas de ítems',
];
