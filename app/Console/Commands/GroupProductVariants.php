<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Product;
use Illuminate\Support\Str;

class GroupProductVariants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:group-variants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically group existing products into a variant system based on common prefixes and suffixes like (S), (M), (L), (F), (5pcs), (10pcs)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::whereNull('parent_id')->get();
        $count = 0;

        foreach ($products as $product) {
            $name = $product->name;
            $parentName = null;
            $variantName = null;

            // Pattern for (S), (M), (L), (F)
            if (preg_match('/^(.*)\s\((S|M|L|F)\)$/', $name, $matches)) {
                $parentName = trim($matches[1]);
                $variantName = $this->getVariantLabel($matches[2]);
            }
            // Pattern for (5pcs), (10pcs)
            elseif (preg_match('/^(.*)\s\((\d+pcs)\)$/', $name, $matches)) {
                $parentName = trim($matches[1]);
                $variantName = $matches[2];
            }
            // Add more patterns if needed

            if ($parentName) {
                // Find or create parent
                $parent = Product::where('name', $parentName)->whereNull('parent_id')->first();
                if (!$parent) {
                    $parent = Product::create([
                        'name' => $parentName,
                        'barcode' => 'P-' . Str::slug($parentName),
                        'price' => $product->price,
                        'quantity' => 0,
                        'status' => 1,
                        'category' => $product->category,
                        'description' => $product->description,
                        'image' => $product->image,
                    ]);
                    $this->info("Created parent: " . $parentName);
                }

                $product->update([
                    'parent_id' => $parent->id,
                    'variant_name' => $variantName,
                ]);
                $count++;
            }
        }

        $this->info("Successfully grouped " . $count . " variants.");
    }

    private function getVariantLabel($code)
    {
        switch ($code) {
            case 'S': return 'Small';
            case 'M': return 'Medium';
            case 'L': return 'Large';
            case 'F': return 'Family';
            default: return $code;
        }
    }
}
