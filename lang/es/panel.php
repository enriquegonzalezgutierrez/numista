<?php

// lang/es/panel.php

return [
    //======================================================================
    // General Navigation & Resource Labels
    //======================================================================
    'nav_items' => 'Ítems',
    'label_item' => 'Ítem',
    'label_items' => 'Ítems',

    'nav_categories' => 'Categorías',
    'label_category' => 'Categoría',
    'label_categories' => 'Categorías',

    //======================================================================
    // General Field Labels (reusable across different forms)
    //======================================================================
    'field_name' => 'Nombre',
    'field_slug' => 'Slug (URL)',
    'field_description' => 'Descripción',
    'field_parent_category' => 'Categoría Superior',
    'field_is_visible' => 'Visible en la web pública',
    'field_items_count' => 'Nº de Ítems',
    'field_new_status' => 'Nuevo Estado',
    'field_categories' => 'Categorías',
    'field_select_categories' => 'Seleccionar categorías',

    //======================================================================
    // Image-related Labels
    //======================================================================
    'label_image_preview' => 'Vista Previa',
    'label_alt_text' => 'Texto Alternativo',
    'helper_alt_text' => 'Describe la imagen para accesibilidad y SEO.', // Versión más concisa

    //======================================================================
    // Table Functionality (Filters, Search)
    //======================================================================
    'filter_type' => 'Filtrar por Tipo',
    'filter_status' => 'Filtrar por Estado',
    'filter_category' => 'Filtrar por Categoría',
    'search_placeholder' => 'Buscar en la colección...',

    //======================================================================
    // Actions (Buttons, Modals, etc.)
    //======================================================================
    // General Actions
    'action_create' => 'Añadir Nuevo',
    'action_edit' => 'Editar',
    'action_delete' => 'Eliminar',
    'action_attach' => 'Asociar',
    'action_detach' => 'Desasociar',

    // Bulk Actions
    'action_bulk_change_status' => 'Cambiar estado de los seleccionados',
    'action_bulk_attach_category' => 'Asignar categoría a los seleccionados',

    // Modal Titles & Buttons
    'modal_attach_title' => 'Asociar Categorías',
    'modal_attach_button' => 'Asociar Selección',
    'modal_create_image_title' => 'Añadir Nueva Imagen',

    //======================================================================
    // Notifications
    //======================================================================
    'notification_status_updated' => 'El estado de los ítems ha sido actualizado.',
    'notification_categories_attached' => 'Las categorías han sido asignadas correctamente.',
];