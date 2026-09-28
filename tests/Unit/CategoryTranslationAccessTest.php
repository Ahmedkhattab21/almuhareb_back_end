<?php

namespace Tests\Unit;

use App\Models\Category;
use Tests\TestCase;

class CategoryTranslationAccessTest extends TestCase
{
    public function test_it_reads_translations_when_loaded_as_array(): void
    {
        $category = new Category(['name' => 'القضايا العمالية']);
        $category->setRelation('translations', [
            ['locale' => 'ar-EG', 'name' => 'القضايا العمالية'],
            ['locale' => 'en-US', 'name' => 'Labor Cases'],
        ]);

        $this->assertSame('Labor Cases', $category->getTranslatedName('en-US'));
        $this->assertSame('القضايا العمالية', $category->getTranslatedName('ar-EG'));
        $this->assertSame('Labor Cases', $category->translationsMap()['en-US']);
    }
}
