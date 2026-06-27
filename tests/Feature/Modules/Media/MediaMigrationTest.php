<?php

use Illuminate\Support\Facades\Schema;

it('creates the canonical media table with the full DAM schema', function () {
    expect(Schema::hasColumns('media', [
        'id', 'driver', 'disk', 'path', 'visibility',
        'name', 'file_name', 'original_file_name', 'mime_type', 'extension', 'size',
        'width', 'height', 'duration', 'checksum',
        'alt_text', 'caption', 'copyright', 'focal_point', 'dominant_color', 'exif',
        'custom_properties', 'uploaded_by', 'deleted_at',
    ]))->toBeTrue();
});

it('creates the media_conversions table', function () {
    expect(Schema::hasColumns('media_conversions', [
        'id', 'media_id', 'conversion_name', 'driver', 'disk', 'path',
        'mime_type', 'format', 'width', 'height', 'size',
    ]))->toBeTrue();
});
