<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Criar a coluna
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('slug')->constrained('categories')->nullOnDelete();
        });

        $allNews = News::whereNotNull('category')->get();

        foreach ($allNews as $news) {
            if (!empty($news->category)) {
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($news->category)],
                    [
                        'name' => $news->category,
                        'text_color' => 'text-purple-500',
                        'bg_color' => 'bg-purple-100'
                    ]
                );

                $news->category_id = $category->id;
                $news->save();
            }
        }

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('category')->nullable()->after('slug');
        });
    }
};
