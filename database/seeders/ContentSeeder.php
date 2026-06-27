<?php

namespace Database\Seeders;

use App\Core\Models\Collection;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Slides
        $this->seedSlides();
        // 2. Services
        $this->seedServices();
        // 3. News
        $this->seedNews();
        // 4. Documents
        $this->seedDocuments();
        // 5. Forum
        $this->seedForum();
    }

    private function seedSlides(): void
    {
        $collection = Collection::firstOrCreate(
            ['slug' => 'slides'],
            ['name' => 'Slides', 'description' => 'Homepage carousel slides.', 'icon' => 'image']
        );
        $blueprint = $collection->blueprints()->firstOrCreate(['name' => 'Default Slide Blueprint']);
        $blueprint->fields()->delete();
        $blueprint->fields()->createMany([
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Subtitle', 'handle' => 'subtitle', 'type' => 'textarea', 'is_translatable' => true],
            ['name' => 'Link', 'handle' => 'link', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Button Text', 'handle' => 'button_text', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
            ['name' => 'Is Active', 'handle' => 'is_active', 'type' => 'boolean', 'is_translatable' => false],
        ]);
    }

    private function seedServices(): void
    {
        $collection = Collection::firstOrCreate(
            ['slug' => 'services'],
            ['name' => 'Services', 'description' => 'Important services and quick links.', 'icon' => 'briefcase']
        );
        $blueprint = $collection->blueprints()->firstOrCreate(['name' => 'Default Service Blueprint']);
        $blueprint->fields()->delete();
        $blueprint->fields()->createMany([
            ['name' => 'Key', 'handle' => 'key', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Icon', 'handle' => 'icon', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Subtitle', 'handle' => 'subtitle', 'type' => 'textarea', 'is_translatable' => true],
            ['name' => 'Is Primary', 'handle' => 'is_primary', 'type' => 'boolean', 'is_translatable' => false],
            ['name' => 'Telephone', 'handle' => 'tel', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Route Key', 'handle' => 'route_key', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
        ]);
    }

    private function seedNews(): void
    {
        $catCollection = Collection::firstOrCreate(
            ['slug' => 'news-categories'],
            ['name' => 'News Categories', 'description' => 'Categories for news articles.', 'icon' => 'folder']
        );
        $catBlueprint = $catCollection->blueprints()->firstOrCreate(['name' => 'Default News Category Blueprint']);
        $catBlueprint->fields()->delete();
        $catBlueprint->fields()->createMany([
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
        ]);

        $newsCollection = Collection::firstOrCreate(
            ['slug' => 'news'],
            ['name' => 'News', 'description' => 'News articles and announcements.', 'icon' => 'newspaper']
        );
        $newsBlueprint = $newsCollection->blueprints()->firstOrCreate(['name' => 'Default News Blueprint']);
        $newsBlueprint->fields()->delete();
        $newsBlueprint->fields()->createMany([
            ['name' => 'Category ID', 'handle' => 'category_id', 'type' => 'relation', 'is_translatable' => false],
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Excerpt', 'handle' => 'excerpt', 'type' => 'textarea', 'is_translatable' => true],
            ['name' => 'Body', 'handle' => 'body', 'type' => 'richtext', 'is_translatable' => true],
            ['name' => 'Author', 'handle' => 'author', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Region', 'handle' => 'region', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Views', 'handle' => 'views', 'type' => 'number', 'is_translatable' => false],
            ['name' => 'Status', 'handle' => 'status', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Published At', 'handle' => 'published_at', 'type' => 'text', 'is_translatable' => false],
        ]);
    }

    private function seedDocuments(): void
    {
        $catCollection = Collection::firstOrCreate(
            ['slug' => 'document-categories'],
            ['name' => 'Document Categories', 'description' => 'Categories for legal documents.', 'icon' => 'folder']
        );
        $catBlueprint = $catCollection->blueprints()->firstOrCreate(['name' => 'Default Document Category Blueprint']);
        $catBlueprint->fields()->delete();
        $catBlueprint->fields()->createMany([
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
        ]);

        $docCollection = Collection::firstOrCreate(
            ['slug' => 'documents'],
            ['name' => 'Documents', 'description' => 'Legal and official documents.', 'icon' => 'document']
        );
        $docBlueprint = $docCollection->blueprints()->firstOrCreate(['name' => 'Default Document Blueprint']);
        $docBlueprint->fields()->delete();
        $docBlueprint->fields()->createMany([
            ['name' => 'Category ID', 'handle' => 'category_id', 'type' => 'relation', 'is_translatable' => false],
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Number', 'handle' => 'number', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Document Date', 'handle' => 'document_date', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Type', 'handle' => 'type', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Size', 'handle' => 'size', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
        ]);
    }

    private function seedForum(): void
    {
        $catCollection = Collection::firstOrCreate(
            ['slug' => 'forum-categories'],
            ['name' => 'Forum Categories', 'description' => 'Categories for community forum.', 'icon' => 'folder']
        );
        $catBlueprint = $catCollection->blueprints()->firstOrCreate(['name' => 'Default Forum Category Blueprint']);
        $catBlueprint->fields()->delete();
        $catBlueprint->fields()->createMany([
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Description', 'handle' => 'description', 'type' => 'textarea', 'is_translatable' => true],
            ['name' => 'Icon', 'handle' => 'icon', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
        ]);

        $topicCollection = Collection::firstOrCreate(
            ['slug' => 'forum-topics'],
            ['name' => 'Forum Topics', 'description' => 'Topics in the community forum.', 'icon' => 'chat']
        );
        $topicBlueprint = $topicCollection->blueprints()->firstOrCreate(['name' => 'Default Forum Topic Blueprint']);
        $topicBlueprint->fields()->delete();
        $topicBlueprint->fields()->createMany([
            ['name' => 'Category ID', 'handle' => 'category_id', 'type' => 'relation', 'is_translatable' => false],
            ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
            ['name' => 'Author', 'handle' => 'author', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Replies', 'handle' => 'replies', 'type' => 'number', 'is_translatable' => false],
            ['name' => 'Views', 'handle' => 'views', 'type' => 'number', 'is_translatable' => false],
            ['name' => 'Pinned', 'handle' => 'pinned', 'type' => 'boolean', 'is_translatable' => false],
            ['name' => 'Last Activity', 'handle' => 'last_activity', 'type' => 'text', 'is_translatable' => false],
            ['name' => 'Sort Order', 'handle' => 'sort_order', 'type' => 'number', 'is_translatable' => false],
        ]);
    }
}
