<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class IngredientsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $ingredients = [

            // ── Poultry ──
            ['name_en' => 'Chicken Breast',         'name_ar' => 'صدر الدجاج'],
            ['name_en' => 'Chicken Thigh',           'name_ar' => 'فخذ الدجاج'],
            ['name_en' => 'Chicken Wings',           'name_ar' => 'أجنحة الدجاج'],
            ['name_en' => 'Chicken Drumstick',       'name_ar' => 'ساق الدجاج'],
            ['name_en' => 'Whole Chicken',           'name_ar' => 'دجاجة كاملة'],
            ['name_en' => 'Turkey Breast',           'name_ar' => 'صدر الديك الرومي'],
            ['name_en' => 'Ground Turkey',           'name_ar' => 'لحم الديك الرومي المفروم'],

            // ── Red Meat ──
            ['name_en' => 'Ground Beef',             'name_ar' => 'لحم البقر المفروم'],
            ['name_en' => 'Beef Steak',              'name_ar' => 'ستيك اللحم'],
            ['name_en' => 'Beef Tenderloin',         'name_ar' => 'فيليه اللحم'],
            ['name_en' => 'Lamb Chops',              'name_ar' => 'ضلوع الضأن'],
            ['name_en' => 'Ground Lamb',             'name_ar' => 'لحم الضأن المفروم'],
            ['name_en' => 'Lamb Leg',                'name_ar' => 'فخذ الضأن'],
            ['name_en' => 'Veal',                    'name_ar' => 'لحم العجل'],
            ['name_en' => 'Beef Liver',              'name_ar' => 'كبد البقر'],

            // ── Seafood ──
            ['name_en' => 'Salmon Fillet',           'name_ar' => 'فيليه السلمون'],
            ['name_en' => 'Tuna',                    'name_ar' => 'تونة'],
            ['name_en' => 'Canned Tuna',             'name_ar' => 'تونة معلبة'],
            ['name_en' => 'Shrimp',                  'name_ar' => 'جمبري'],
            ['name_en' => 'Tilapia',                 'name_ar' => 'سمك البلطي'],
            ['name_en' => 'Sardines',                'name_ar' => 'سردين'],
            ['name_en' => 'Cod Fillet',              'name_ar' => 'فيليه سمك القد'],
            ['name_en' => 'Squid',                   'name_ar' => 'حبار'],

            // ── Eggs ──
            ['name_en' => 'Whole Egg',               'name_ar' => 'بيضة كاملة'],
            ['name_en' => 'Egg White',               'name_ar' => 'بياض البيض'],
            ['name_en' => 'Egg Yolk',                'name_ar' => 'صفار البيض'],

            // ── Plant Protein ──
            ['name_en' => 'Tofu',                    'name_ar' => 'توفو'],
            ['name_en' => 'Tempeh',                  'name_ar' => 'تمبيه'],
            ['name_en' => 'Edamame',                 'name_ar' => 'إيداماميه'],

            // ── Grains and Carbs ──
            ['name_en' => 'White Rice',              'name_ar' => 'أرز أبيض'],
            ['name_en' => 'Brown Rice',              'name_ar' => 'أرز بني'],
            ['name_en' => 'Basmati Rice',            'name_ar' => 'أرز بسمتي'],
            ['name_en' => 'Pasta',                   'name_ar' => 'باستا'],
            ['name_en' => 'Spaghetti',               'name_ar' => 'سباغيتي'],
            ['name_en' => 'Penne',                   'name_ar' => 'بيني'],
            ['name_en' => 'White Bread',             'name_ar' => 'خبز أبيض'],
            ['name_en' => 'Whole Wheat Bread',       'name_ar' => 'خبز القمح الكامل'],
            ['name_en' => 'Pita Bread',              'name_ar' => 'خبز عربي'],
            ['name_en' => 'Rolled Oats',             'name_ar' => 'شوفان'],
            ['name_en' => 'Quinoa',                  'name_ar' => 'كينوا'],
            ['name_en' => 'Bulgur',                  'name_ar' => 'برغل'],
            ['name_en' => 'Couscous',                'name_ar' => 'كسكس'],
            ['name_en' => 'White Flour',             'name_ar' => 'دقيق أبيض'],
            ['name_en' => 'Whole Wheat Flour',       'name_ar' => 'دقيق القمح الكامل'],
            ['name_en' => 'Couscous',                'name_ar' => 'كسكس'],
            ['name_en' => 'Cornmeal',                'name_ar' => 'دقيق الذرة'],

            // ── Potatoes and Starches ──
            ['name_en' => 'White Potato',            'name_ar' => 'بطاطس بيضاء'],
            ['name_en' => 'Sweet Potato',            'name_ar' => 'بطاطا حلوة'],
            ['name_en' => 'Cassava',                 'name_ar' => 'كاسافا'],

            // ── Vegetables ──
            ['name_en' => 'Tomato',                  'name_ar' => 'طماطم'],
            ['name_en' => 'Cherry Tomato',           'name_ar' => 'طماطم كرزية'],
            ['name_en' => 'Cucumber',                'name_ar' => 'خيار'],
            ['name_en' => 'White Onion',             'name_ar' => 'بصل أبيض'],
            ['name_en' => 'Red Onion',               'name_ar' => 'بصل أحمر'],
            ['name_en' => 'Spring Onion',            'name_ar' => 'بصل أخضر'],
            ['name_en' => 'Garlic',                  'name_ar' => 'ثوم'],
            ['name_en' => 'Spinach',                 'name_ar' => 'سبانخ'],
            ['name_en' => 'Carrot',                  'name_ar' => 'جزر'],
            ['name_en' => 'Broccoli',                'name_ar' => 'بروكلي'],
            ['name_en' => 'Cauliflower',             'name_ar' => 'قرنبيط'],
            ['name_en' => 'Cabbage',                 'name_ar' => 'ملفوف'],
            ['name_en' => 'Lettuce',                 'name_ar' => 'خس'],
            ['name_en' => 'Rocket Leaves',           'name_ar' => 'جرجير'],
            ['name_en' => 'Bell Pepper',             'name_ar' => 'فلفل رومي'],
            ['name_en' => 'Green Chili',             'name_ar' => 'فلفل أخضر حار'],
            ['name_en' => 'Zucchini',                'name_ar' => 'كوسا'],
            ['name_en' => 'Eggplant',                'name_ar' => 'باذنجان'],
            ['name_en' => 'Mushroom',                'name_ar' => 'فطر'],
            ['name_en' => 'Corn',                    'name_ar' => 'ذرة'],
            ['name_en' => 'Green Beans',             'name_ar' => 'فاصوليا خضراء'],
            ['name_en' => 'Peas',                    'name_ar' => 'بازلاء'],
            ['name_en' => 'Celery',                  'name_ar' => 'كرفس'],
            ['name_en' => 'Leek',                    'name_ar' => 'كراث'],
            ['name_en' => 'Beetroot',                'name_ar' => 'شمندر'],
            ['name_en' => 'Radish',                  'name_ar' => 'فجل'],
            ['name_en' => 'Artichoke',               'name_ar' => 'أرضي شوكي'],

            // ── Fruits ──
            ['name_en' => 'Banana',                  'name_ar' => 'موز'],
            ['name_en' => 'Apple',                   'name_ar' => 'تفاح'],
            ['name_en' => 'Orange',                  'name_ar' => 'برتقال'],
            ['name_en' => 'Lemon',                   'name_ar' => 'ليمون'],
            ['name_en' => 'Lime',                    'name_ar' => 'ليمون أخضر'],
            ['name_en' => 'Strawberry',              'name_ar' => 'فراولة'],
            ['name_en' => 'Mango',                   'name_ar' => 'مانجو'],
            ['name_en' => 'Watermelon',              'name_ar' => 'بطيخ'],
            ['name_en' => 'Grapes',                  'name_ar' => 'عنب'],
            ['name_en' => 'Pomegranate',             'name_ar' => 'رمان'],
            ['name_en' => 'Peach',                   'name_ar' => 'خوخ'],
            ['name_en' => 'Pear',                    'name_ar' => 'إجاص'],
            ['name_en' => 'Pineapple',               'name_ar' => 'أناناس'],
            ['name_en' => 'Kiwi',                    'name_ar' => 'كيوي'],
            ['name_en' => 'Avocado',                 'name_ar' => 'أفوكادو'],
            ['name_en' => 'Dates',                   'name_ar' => 'تمر'],
            ['name_en' => 'Fig',                     'name_ar' => 'تين'],

            // ── Dairy ──
            ['name_en' => 'Whole Milk',              'name_ar' => 'حليب كامل الدسم'],
            ['name_en' => 'Skimmed Milk',            'name_ar' => 'حليب خالي الدسم'],
            ['name_en' => 'Plain Yogurt',            'name_ar' => 'زبادي سادة'],
            ['name_en' => 'Greek Yogurt',            'name_ar' => 'زبادي يوناني'],
            ['name_en' => 'Butter',                  'name_ar' => 'زبدة'],
            ['name_en' => 'Cheddar Cheese',          'name_ar' => 'جبن شيدر'],
            ['name_en' => 'Mozzarella Cheese',       'name_ar' => 'جبن موزاريلا'],
            ['name_en' => 'Feta Cheese',             'name_ar' => 'جبن فيتا'],
            ['name_en' => 'Cream Cheese',            'name_ar' => 'جبن كريمي'],
            ['name_en' => 'Heavy Cream',             'name_ar' => 'كريمة خثيفة'],
            ['name_en' => 'Whipping Cream',          'name_ar' => 'كريمة للخفق'],
            ['name_en' => 'Labneh',                  'name_ar' => 'لبنة'],
            ['name_en' => 'Halloumi',                'name_ar' => 'جبن حلومي'],

            // ── Oils and Fats ──
            ['name_en' => 'Olive Oil',               'name_ar' => 'زيت الزيتون'],
            ['name_en' => 'Vegetable Oil',           'name_ar' => 'زيت نباتي'],
            ['name_en' => 'Coconut Oil',             'name_ar' => 'زيت جوز الهند'],
            ['name_en' => 'Sunflower Oil',           'name_ar' => 'زيت عباد الشمس'],
            ['name_en' => 'Sesame Oil',              'name_ar' => 'زيت السمسم'],
            ['name_en' => 'Ghee',                    'name_ar' => 'سمن'],

            // ── Legumes ──
            ['name_en' => 'Red Lentils',             'name_ar' => 'عدس أحمر'],
            ['name_en' => 'Brown Lentils',           'name_ar' => 'عدس بني'],
            ['name_en' => 'Chickpeas',               'name_ar' => 'حمص'],
            ['name_en' => 'Black Beans',             'name_ar' => 'فاصوليا سوداء'],
            ['name_en' => 'Kidney Beans',            'name_ar' => 'فاصوليا حمراء'],
            ['name_en' => 'White Beans',             'name_ar' => 'فاصوليا بيضاء'],
            ['name_en' => 'Fava Beans',              'name_ar' => 'فول'],
            ['name_en' => 'Soybeans',                'name_ar' => 'فول الصويا'],

            // ── Nuts and Seeds ──
            ['name_en' => 'Almonds',                 'name_ar' => 'لوز'],
            ['name_en' => 'Walnuts',                 'name_ar' => 'جوز'],
            ['name_en' => 'Cashews',                 'name_ar' => 'كاجو'],
            ['name_en' => 'Peanuts',                 'name_ar' => 'فول سوداني'],
            ['name_en' => 'Pistachios',              'name_ar' => 'فستق'],
            ['name_en' => 'Sesame Seeds',            'name_ar' => 'سمسم'],
            ['name_en' => 'Sunflower Seeds',         'name_ar' => 'بذور عباد الشمس'],
            ['name_en' => 'Pumpkin Seeds',           'name_ar' => 'بذور اليقطين'],
            ['name_en' => 'Chia Seeds',              'name_ar' => 'بذور الشيا'],
            ['name_en' => 'Flaxseeds',               'name_ar' => 'بذور الكتان'],
            ['name_en' => 'Peanut Butter',           'name_ar' => 'زبدة الفول السوداني'],
            ['name_en' => 'Almond Butter',           'name_ar' => 'زبدة اللوز'],
            ['name_en' => 'Tahini',                  'name_ar' => 'طحينة'],

            // ── Spices and Herbs ──
            ['name_en' => 'Salt',                    'name_ar' => 'ملح'],
            ['name_en' => 'Black Pepper',            'name_ar' => 'فلفل أسود'],
            ['name_en' => 'Cumin',                   'name_ar' => 'كمون'],
            ['name_en' => 'Turmeric',                'name_ar' => 'كركم'],
            ['name_en' => 'Cinnamon',                'name_ar' => 'قرفة'],
            ['name_en' => 'Paprika',                 'name_ar' => 'بابريكا'],
            ['name_en' => 'Cayenne Pepper',          'name_ar' => 'فلفل حار'],
            ['name_en' => 'Cardamom',                'name_ar' => 'هيل'],
            ['name_en' => 'Coriander',               'name_ar' => 'كزبرة'],
            ['name_en' => 'Oregano',                 'name_ar' => 'زعتر أخضر'],
            ['name_en' => 'Thyme',                   'name_ar' => 'زعتر'],
            ['name_en' => 'Rosemary',                'name_ar' => 'إكليل الجبل'],
            ['name_en' => 'Basil',                   'name_ar' => 'ريحان'],
            ['name_en' => 'Parsley',                 'name_ar' => 'بقدونس'],
            ['name_en' => 'Mint',                    'name_ar' => 'نعناع'],
            ['name_en' => 'Ginger',                  'name_ar' => 'زنجبيل'],
            ['name_en' => 'Garlic Powder',           'name_ar' => 'مسحوق الثوم'],
            ['name_en' => 'Onion Powder',            'name_ar' => 'مسحوق البصل'],
            ['name_en' => 'Bay Leaves',              'name_ar' => 'ورق الغار'],
            ['name_en' => 'Nutmeg',                  'name_ar' => 'جوزة الطيب'],
            ['name_en' => 'Cloves',                  'name_ar' => 'قرنفل'],
            ['name_en' => 'Allspice',                'name_ar' => 'بهارات مشكلة'],
            ['name_en' => 'Saffron',                 'name_ar' => 'زعفران'],
            ['name_en' => 'Vanilla Extract',         'name_ar' => 'خلاصة الفانيليا'],

            // ── Condiments and Sauces ──
            ['name_en' => 'Honey',                   'name_ar' => 'عسل'],
            ['name_en' => 'Ketchup',                 'name_ar' => 'كاتشب'],
            ['name_en' => 'Mayonnaise',              'name_ar' => 'مايونيز'],
            ['name_en' => 'Soy Sauce',               'name_ar' => 'صلصة الصويا'],
            ['name_en' => 'Tomato Paste',            'name_ar' => 'معجون الطماطم'],
            ['name_en' => 'Tomato Sauce',            'name_ar' => 'صلصة الطماطم'],
            ['name_en' => 'Hot Sauce',               'name_ar' => 'صلصة حارة'],
            ['name_en' => 'Mustard',                 'name_ar' => 'خردل'],
            ['name_en' => 'White Vinegar',           'name_ar' => 'خل أبيض'],
            ['name_en' => 'Apple Cider Vinegar',     'name_ar' => 'خل التفاح'],
            ['name_en' => 'Worcestershire Sauce',    'name_ar' => 'صلصة وورشستر'],
            ['name_en' => 'Pomegranate Molasses',    'name_ar' => 'دبس الرمان'],
            ['name_en' => 'Date Syrup',              'name_ar' => 'دبس التمر'],

            // ── Sweeteners ──
            ['name_en' => 'White Sugar',             'name_ar' => 'سكر أبيض'],
            ['name_en' => 'Brown Sugar',             'name_ar' => 'سكر بني'],
            ['name_en' => 'Powdered Sugar',          'name_ar' => 'سكر ناعم'],
            ['name_en' => 'Maple Syrup',             'name_ar' => 'شراب القيقب'],
            ['name_en' => 'Stevia',                  'name_ar' => 'ستيفيا'],

            // ── Baking ──
            ['name_en' => 'Baking Powder',           'name_ar' => 'بيكنج بودر'],
            ['name_en' => 'Baking Soda',             'name_ar' => 'صودا الخبز'],
            ['name_en' => 'Yeast',                   'name_ar' => 'خميرة'],
            ['name_en' => 'Cocoa Powder',            'name_ar' => 'مسحوق الكاكاو'],
            ['name_en' => 'Dark Chocolate',          'name_ar' => 'شوكولاتة داكنة'],
            ['name_en' => 'Cornstarch',              'name_ar' => 'نشا الذرة'],

            // ── Beverages and Liquids ──
            ['name_en' => 'Water',                   'name_ar' => 'ماء'],
            ['name_en' => 'Coconut Milk',            'name_ar' => 'حليب جوز الهند'],
            ['name_en' => 'Almond Milk',             'name_ar' => 'حليب اللوز'],
            ['name_en' => 'Chicken Broth',           'name_ar' => 'مرق الدجاج'],
            ['name_en' => 'Beef Broth',              'name_ar' => 'مرق اللحم'],
            ['name_en' => 'Vegetable Broth',         'name_ar' => 'مرق الخضار'],

        ];

        $rows = array_map(fn($item) => array_merge($item, [
            'source'     => 'system',
            'verified'   => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]), $ingredients);

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('ingredients')->insertOrIgnore($chunk);
        }
    }
}
