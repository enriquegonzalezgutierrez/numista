<?php
// lang/es/item.php

return [
    // --- Section Titles in Item Form ---
    'section_core' => 'Datos Principales del Ítem',
    'section_acquisition' => 'Datos de Adquisición y Venta',

    // --- Core Item Fields (used in Filament forms and tables) ---
    'field_name' => 'Nombre',
    'field_type' => 'Tipo de Ítem',
    'field_description' => 'Descripción',
    'field_quantity' => 'Cantidad',
    'field_purchase_price' => 'Precio de Compra',
    'field_purchase_date' => 'Fecha de Compra',
    'field_status' => 'Estado Actual',
    'field_sale_price' => 'Precio de Venta',
    'field_unit_price' => 'Precio Unitario',

    // --- Item Type Options ---
    'type_art' => 'Obra de Arte',
    'type_antique' => 'Antigüedad',
    'type_banknote' => 'Billete',
    'type_book' => 'Libro / Manuscrito',
    'type_camera' => 'Cámara / Equipo Fotográfico',
    'type_coin' => 'Moneda',
    'type_comic' => 'Cómic / Tebeo',
    'type_craftsmanship' => 'Artesanía',
    'type_jewelry' => 'Joya / Orfebrería',
    'type_medal' => 'Medalla / Condecoración',
    'type_military' => 'Militaria / Coleccionismo Militar',
    'type_movie_collectible' => 'Póster / Cine',
    'type_object' => 'Objeto de Colección',
    'type_paper' => 'Documento / Coleccionismo de Papel',
    'type_pen' => 'Instrumento de Escritura',
    'type_photo' => 'Fotografía Antigua',
    'type_postcard' => 'Tarjeta Postal',
    'type_radio' => 'Radio / Gramófono',
    'type_sports' => 'Memorabilia Deportiva',
    'type_stamp' => 'Sello / Filatelia',
    'type_toy' => 'Juguete / Juego',
    'type_vehicle' => 'Vehículo Clásico',
    'type_vintage_item' => 'Objeto Vintage',
    'type_vinyl_record' => 'Disco / Vinilo',
    'type_watch' => 'Reloj',

    // --- Status Options ---
    'status_in_collection' => 'En mi colección',
    'status_for_sale' => 'En venta',
    'status_sold' => 'Vendido',
    'status_featured' => 'Destacado',
    'status_discounted' => 'En oferta',
    'status_pending' => 'Pendiente',
    'status_paid' => 'Pagado',
    'status_shipped' => 'Enviado',
    'status_completed' => 'Completado',
    'status_cancelled' => 'Cancelado',
    
    // --- Option Definitions for Selectable Attributes ---
    'options' => [
        'grade' => [ // This key should match the attribute name in lowercase: 'grado' -> 'grade'
            'unc' => 'UNC (Sin Circular)',
            'au' => 'AU (Casi Sin Circular)',
            'xf' => 'XF (Excelentemente Conservado)',
            'vf' => 'VF (Muy Bien Conservado)',
            'f' => 'F (Bien Conservado)',
            'g' => 'G (Regular)',
        ],
    ],
];