<?php

namespace Database\Seeders;

use App\Enums\MealVisibility;
use App\Models\MealMacro;
use App\Models\MealPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdminMealPostsSeeder extends Seeder
{
    private array $meals = [
        // [name, description, calories, protein, carbs, fats, fiber]
        ['Grilled Chicken Bowl',            'High-protein bowl with rice and veggies',              520, 45, 38, 14, 6],
        ['Avocado Toast',                   'Whole grain toast topped with fresh avocado',           390, 12, 42, 20, 8],
        ['Salmon & Quinoa',                 'Omega-3 rich salmon fillet with quinoa',                580, 48, 35, 18, 5],
        ['Greek Yogurt Parfait',            'Layered yogurt with berries and granola',               320, 18, 44, 7,  3],
        ['Beef Stir Fry',                   'Lean beef with mixed vegetables',                       610, 42, 40, 22, 7],
        ['Oatmeal with Fruits',             'Steel-cut oats topped with banana and berries',         380, 14, 68, 6,  9],
        ['Tuna Salad Wrap',                 'Light tuna with cucumber and lettuce wrap',             420, 36, 32, 12, 4],
        ['Protein Smoothie',                'Banana, peanut butter, and protein powder',             450, 35, 48, 14, 5],
        ['Lentil Soup',                     'Hearty red lentil soup with spices',                    360, 22, 55, 5,  12],
        ['Turkey Sandwich',                 'Whole wheat with turkey and greens',                    480, 38, 45, 12, 6],
        ['Egg White Omelette',              'Three-egg-white omelette with spinach',                 280, 28, 8,  10, 2],
        ['Sweet Potato & Chicken',          'Baked chicken breast with sweet potato',                510, 44, 48, 8,  7],
        ['Cottage Cheese Bowl',             'Low-fat cottage cheese with pineapple',                 310, 26, 38, 5,  2],
        ['Brown Rice & Black Beans',        'Plant-based protein with whole grain rice',             440, 20, 78, 4,  14],
        ['Shrimp Tacos',                    'Grilled shrimp in corn tortillas',                      470, 32, 52, 14, 5],
        ['Veggie Buddha Bowl',              'Roasted vegetables over farro',                         400, 16, 60, 12, 10],
        ['Chicken Caesar Salad',            'Romaine with grilled chicken and light dressing',       490, 40, 22, 22, 4],
        ['Whey Protein Pancakes',           'High-protein pancakes with maple syrup',                430, 38, 50, 8,  3],
        ['Baked Cod & Asparagus',           'White fish with roasted asparagus',                     340, 42, 12, 8,  5],
        ['Almond Butter Rice Cakes',        'Brown rice cakes with almond butter',                   350, 10, 44, 16, 4],
        ['Pesto Pasta with Chicken',        'Whole wheat pasta with homemade pesto',                 580, 38, 62, 18, 7],
        ['Overnight Oats',                  'Oats soaked in almond milk with chia seeds',            370, 15, 56, 10, 8],
        ['Beef & Broccoli',                 'Lean beef with steamed broccoli and sauce',             530, 44, 36, 18, 6],
        ['Fruit & Nut Mix',                 'Trail mix with dried fruits and almonds',               400, 12, 50, 18, 5],
        ['Stuffed Bell Peppers',            'Ground turkey stuffed peppers with cheese',             460, 36, 38, 16, 6],
        ['Chickpea Curry',                  'Spiced chickpeas in tomato-coconut sauce',              480, 18, 70, 14, 12],
        ['Protein Energy Balls',            'No-bake oat balls with dates and protein',              380, 20, 52, 12, 6],
        ['Zucchini Noodles & Meatballs',    'Low-carb zucchini pasta with beef meatballs',           420, 40, 18, 20, 5],
        ['Chia Pudding',                    'Coconut milk chia pudding with mango',                  350, 10, 46, 14, 10],
        ['Loaded Baked Potato',             'Sweet potato topped with Greek yogurt',                 500, 22, 80, 8,  9],
        ['Teriyaki Salmon Bowl',            'Glazed salmon over steamed jasmine rice',               560, 44, 52, 16, 4],
        ['Mango Chicken Salad',             'Grilled chicken with fresh mango and lime dressing',    430, 38, 34, 12, 5],
        ['Black Bean Burrito',              'Whole wheat tortilla with seasoned black beans',        510, 22, 72, 14, 13],
        ['Bison Burger',                    'Lean bison patty on a whole grain bun',                 590, 48, 38, 22, 4],
        ['Spinach Feta Omelette',           'Two-egg omelette with spinach and feta cheese',         310, 24, 6,  20, 2],
        ['Tofu Scramble',                   'Seasoned tofu scramble with peppers and onions',        340, 22, 20, 18, 5],
        ['Miso Soup with Edamame',          'Light miso broth with edamame and tofu',                220, 18, 16, 8,  6],
        ['Acai Bowl',                       'Blended acai topped with granola and berries',          480, 10, 72, 16, 9],
        ['Turkey Meatballs & Ziti',         'Lean turkey meatballs over whole grain ziti',           570, 42, 64, 14, 7],
        ['Cauliflower Fried Rice',          'Low-carb cauliflower rice stir-fried with veggies',     280, 18, 22, 12, 7],
        ['Smoked Salmon Bagel',             'Whole grain bagel with smoked salmon and cream cheese', 490, 30, 52, 16, 3],
        ['Peanut Butter Banana Wrap',       'Whole wheat wrap with peanut butter and banana',        440, 14, 60, 16, 6],
        ['Grilled Halloumi Salad',          'Seared halloumi over mixed greens with olives',         420, 22, 18, 28, 4],
        ['Spicy Tofu Tacos',                'Crispy tofu in corn tortillas with sriracha slaw',      400, 20, 48, 14, 8],
        ['Lamb Kofta Plate',                'Spiced lamb kofta with tabbouleh and hummus',           620, 42, 40, 30, 6],
        ['Watermelon Feta Salad',           'Fresh watermelon, feta, mint, and arugula',             240, 8,  32, 10, 2],
        ['Mushroom Risotto',                'Creamy arborio rice with mixed mushrooms',               520, 14, 74, 18, 4],
        ['Baked Turkey Meatloaf',           'Lean turkey meatloaf with tomato glaze',                480, 44, 30, 18, 3],
        ['Veggie Omelette',                 'Three-egg omelette packed with garden vegetables',      340, 26, 12, 18, 4],
        ['Soba Noodle Bowl',                'Cold soba noodles with edamame and sesame dressing',    430, 20, 64, 10, 7],
        ['Chicken Shawarma Wrap',           'Marinated chicken in a whole wheat wrap with tzatziki', 540, 42, 46, 18, 5],
        ['Blueberry Protein Muffins',       'Baked protein muffins loaded with blueberries',         310, 20, 38, 8,  4],
        ['Pad Thai with Shrimp',            'Rice noodles stir-fried with shrimp and tamarind',      520, 32, 62, 14, 4],
        ['Roasted Vegetable Soup',          'Blended roasted tomato and pepper soup',                260, 8,  38, 8,  9],
        ['Chicken & Waffle',                'Grilled chicken breast on a whole grain waffle',        550, 40, 56, 16, 4],
        ['Edamame & Quinoa Bowl',           'Protein-packed quinoa with shelled edamame',            430, 24, 58, 10, 11],
        ['Beef Tacos',                      'Seasoned lean beef in corn tortillas with salsa',       510, 36, 46, 18, 5],
        ['Ricotta & Berry Toast',           'Whole grain toast with whipped ricotta and berries',    360, 16, 44, 12, 5],
        ['Canned Sardine Salad',            'Sardines over arugula with lemon vinaigrette',          310, 30, 10, 16, 3],
        ['Shakshuka',                       'Eggs poached in spiced tomato and pepper sauce',        380, 22, 28, 20, 6],
        ['Mashed Cauliflower & Steak',      'Creamy cauliflower mash with grilled flank steak',      560, 48, 20, 30, 7],
        ['Pineapple Chicken Fried Rice',    'Lightly fried rice with pineapple chunks and chicken',  530, 34, 64, 12, 4],
        ['Apple Cinnamon Oatmeal',          'Creamy oats with diced apple and cinnamon',             360, 10, 64, 7,  8],
        ['Sesame Ginger Tofu Bowl',         'Marinated tofu over brown rice with sesame sauce',      450, 22, 56, 16, 7],
        ['Chicken Noodle Soup',             'Classic lean chicken soup with egg noodles',            380, 32, 36, 8,  4],
        ['Kale & Sweet Potato Hash',        'Sautéed kale with roasted sweet potato and eggs',       410, 20, 50, 14, 9],
        ['Mango Lassi Smoothie',            'Blended mango with yogurt and cardamom',                310, 12, 54, 5,  3],
        ['Baked Falafel Plate',             'Oven-baked falafel with tabouleh and pita',             520, 18, 72, 16, 12],
        ['Pork Tenderloin & Greens',        'Roasted pork tenderloin with sautéed chard',            490, 46, 14, 24, 5],
        ['Tomato Basil Frittata',           'Baked egg frittata with cherry tomatoes and basil',     330, 24, 10, 20, 3],
        ['Spelt Porridge',                  'Slow-cooked spelt with honey and walnuts',              400, 14, 62, 12, 6],
        ['Chicken Tikka Masala',            'Grilled chicken in a creamy spiced tomato sauce',       580, 44, 38, 24, 5],
        ['Tabbouleh Bowl',                  'Bulgur wheat with parsley, tomato, and lemon',          340, 10, 58, 8,  10],
        ['Protein Waffles',                 'Crispy waffles made with whey protein batter',          410, 34, 44, 10, 3],
        ['Coconut Chicken Curry',           'Chicken thighs simmered in coconut milk curry',         600, 42, 36, 28, 5],
        ['Egg & Spinach Breakfast Bowl',    'Scrambled eggs over sautéed spinach and tomatoes',      300, 24, 10, 16, 4],
        ['Honey Sriracha Shrimp Bowl',      'Glazed shrimp over jasmine rice with cucumber',         490, 34, 58, 10, 3],
        ['Barley & Roasted Beet Salad',     'Pearl barley with roasted beets and goat cheese',       420, 14, 62, 14, 8],
        ['Turkey & Hummus Pita',            'Whole wheat pita with sliced turkey and hummus',        460, 34, 48, 14, 6],
        ['Almond Flour Banana Bread',       'Moist banana bread made with almond flour',             350, 10, 42, 16, 4],
        ['Prawn & Mango Salad',             'Chilled prawns with mango, avocado, and lime',          370, 28, 30, 14, 5],
        ['French Lentils & Poached Egg',    'Green lentils with a runny poached egg on top',         420, 28, 52, 10, 14],
        ['Grilled Veggie Skewers',          'Colorful vegetable skewers with herb marinade',         280, 8,  40, 10, 8],
        ['Mackerel & Brown Rice',           'Smoked mackerel fillet over nutty brown rice',          530, 40, 48, 18, 4],
        ['Cinnamon Protein Shake',          'Whey protein blended with almond milk and cinnamon',    320, 36, 28, 6,  2],
        ['Chicken Souvlaki Plate',          'Marinated chicken skewers with Greek salad',            550, 48, 28, 24, 5],
        ['Edamame Avocado Toast',           'Smashed edamame and avocado on sourdough',              410, 16, 44, 18, 9],
        ['Beef Bulgogi Bowl',               'Korean-marinated beef over steamed rice',               570, 44, 52, 18, 3],
        ['Spicy Lentil Dahl',               'Red lentils slow-cooked with cumin and chilli',         400, 20, 60, 8,  13],
        ['Caprese Omelette',                'Egg omelette with fresh mozzarella and tomato',         360, 26, 8,  22, 2],
        ['Millet & Roasted Pepper Bowl',    'Fluffy millet with charred red peppers and feta',       390, 14, 58, 12, 7],
        ['Chicken Fajita Bowl',             'Spiced chicken strips with peppers over rice',          520, 40, 50, 14, 6],
        ['Walnut & Date Energy Bar',        'No-bake bar with walnuts, dates, and oats',             370, 10, 52, 14, 5],
        ['Harissa Roasted Salmon',          'Salmon fillet glazed with harissa paste',               510, 44, 16, 28, 3],
        ['Tofu Miso Ramen',                 'Miso broth ramen with tofu and soft-boiled egg',        470, 26, 58, 14, 5],
        ['Whipped Feta Dip & Veggies',      'Creamy whipped feta with raw vegetable dippers',        290, 12, 22, 18, 5],
        ['Pumpkin Protein Soup',            'Silky pumpkin soup with a protein boost',               300, 16, 38, 8,  7],
        ['Tuna Nicoise Salad',              'Classic nicoise with tuna, eggs, and olives',           460, 36, 24, 22, 6],
        ['Chicken Pho',                     'Vietnamese-style chicken broth with rice noodles',      430, 34, 48, 8,  3],
    ];

    public function run(): void
    {
        $user = User::where('email', env('ADMIN_EMAIL', 'unknown@example.com'))->firstOrFail();

        $profileId = $user->profile->id;

        MealPost::whereHas('userProfile', fn($q) => $q->where('user_id', $user->id))->forceDelete();

        $now = Carbon::now();

        foreach ($this->meals as $index => $meal) {
            [$name, $description, $calories, $protein, $carbs, $fats, $fiber] = $meal;

            $fingerprint = hash('sha256', "{$name}_{$calories}_{$protein}_{$carbs}_{$fats}");

            MealMacro::firstOrCreate(
                ['fingerprint' => $fingerprint],
                [
                    'calories' => $calories,
                    'protein'  => $protein,
                    'carbs'    => $carbs,
                    'fats'     => $fats,
                    'fiber'    => $fiber,
                ]
            );

            $visibility = $index % 2 === 0 ? MealVisibility::PUBLIC : MealVisibility::PRIVATE;

            MealPost::create([
                'user_profile_id' => $profileId,
                'fingerprint'     => $fingerprint,
                'name'            => $name,
                'description'     => $description,
                'visibility'      => $visibility,
                'image_url'       => null,
                'confirmed_at'    => $now->copy()->subMinutes($index * 10),
                'likes_count'     => rand(0, 50),
                'relogs_count'    => rand(0, 20),
                'comments_count'  => rand(0, 15),
                'servings'        => rand(1, 4),
            ]);
        }

        $public  = collect($this->meals)->filter(fn($_, $i) => $i % 2 === 0)->count();
        $private = collect($this->meals)->filter(fn($_, $i) => $i % 2 !== 0)->count();

        $this->command->info("Seeded {$public} public and {$private} private meal posts for admin.");
    }
}
