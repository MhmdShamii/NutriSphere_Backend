<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HealthConditionsSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [

            // ========================
            // METABOLIC & CARDIO
            // ========================
            ["name" => "Obesity",                "slug" => "obesity",                "type" => "disease",     "severity" => "adjust"],
            ["name" => "Metabolic Syndrome",     "slug" => "metabolic_syndrome",     "type" => "disease",     "severity" => "adjust"],
            ["name" => "Heart Disease",          "slug" => "heart_disease",          "type" => "disease",     "severity" => "adjust"],
            ["name" => "High Cholesterol",       "slug" => "high_cholesterol",       "type" => "disease",     "severity" => "adjust"],
            ["name" => "Hypertension",           "slug" => "hypertension",           "type" => "disease",     "severity" => "adjust"],
            ["name" => "Atherosclerosis",        "slug" => "atherosclerosis",        "type" => "disease",     "severity" => "adjust"],

            // ========================
            // ENDOCRINE / BLOOD SUGAR
            // ========================
            ["name" => "Diabetes Type 1",        "slug" => "diabetes_type_1",        "type" => "disease",     "severity" => "adjust"],
            ["name" => "Diabetes Type 2",        "slug" => "diabetes_type_2",        "type" => "disease",     "severity" => "adjust"],
            ["name" => "Prediabetes",            "slug" => "prediabetes",            "type" => "disease",     "severity" => "adjust"],
            ["name" => "Insulin Resistance",     "slug" => "insulin_resistance",     "type" => "disease",     "severity" => "adjust"],
            ["name" => "Thyroid Disease",        "slug" => "thyroid_disease",        "type" => "disease",     "severity" => "adjust"],
            ["name" => "PCOS",                   "slug" => "pcos",                   "type" => "disease",     "severity" => "adjust"],
            ["name" => "Gout",                   "slug" => "gout",                   "type" => "disease",     "severity" => "adjust"],

            // ========================
            // DIGESTIVE SYSTEM
            // ========================
            ["name" => "IBS",                    "slug" => "ibs",                    "type" => "disease",     "severity" => "warn"],
            ["name" => "GERD (Acid Reflux)",     "slug" => "gerd",                   "type" => "disease",     "severity" => "warn"],
            ["name" => "Gastritis",              "slug" => "gastritis",              "type" => "disease",     "severity" => "warn"],
            ["name" => "Crohn's Disease",        "slug" => "crohns_disease",         "type" => "disease",     "severity" => "warn"],
            ["name" => "Ulcerative Colitis",     "slug" => "ulcerative_colitis",     "type" => "disease",     "severity" => "warn"],
            ["name" => "Celiac Disease",         "slug" => "celiac_disease",         "type" => "disease",     "severity" => "block"],

            // ========================
            // LIVER & KIDNEY
            // ========================
            ["name" => "Fatty Liver Disease",    "slug" => "fatty_liver",            "type" => "disease",     "severity" => "adjust"],
            ["name" => "Liver Disease",          "slug" => "liver_disease",          "type" => "disease",     "severity" => "adjust"],
            ["name" => "Kidney Disease",         "slug" => "kidney_disease",         "type" => "disease",     "severity" => "adjust"],

            // ========================
            // BONE & JOINT
            // ========================
            ["name" => "Osteoporosis",           "slug" => "osteoporosis",           "type" => "disease",     "severity" => "adjust"],
            ["name" => "Arthritis",              "slug" => "arthritis",              "type" => "disease",     "severity" => "adjust"],

            // ========================
            // RESPIRATORY
            // ========================
            ["name" => "Asthma",                 "slug" => "asthma",                 "type" => "disease",     "severity" => "warn"],
            ["name" => "COPD",                   "slug" => "copd",                   "type" => "disease",     "severity" => "adjust"],

            // ========================
            // ALLERGIES
            // ========================
            ["name" => "Nut Allergy",            "slug" => "nut_allergy",            "type" => "allergy",     "severity" => "block"],
            ["name" => "Shellfish Allergy",      "slug" => "shellfish_allergy",      "type" => "allergy",     "severity" => "block"],
            ["name" => "Fish Allergy",           "slug" => "fish_allergy",           "type" => "allergy",     "severity" => "block"],
            ["name" => "Egg Allergy",            "slug" => "egg_allergy",            "type" => "allergy",     "severity" => "block"],
            ["name" => "Milk Allergy",           "slug" => "milk_allergy",           "type" => "allergy",     "severity" => "block"],
            ["name" => "Wheat Allergy",          "slug" => "wheat_allergy",          "type" => "allergy",     "severity" => "block"],
            ["name" => "Soy Allergy",            "slug" => "soy_allergy",            "type" => "allergy",     "severity" => "block"],

            // ========================
            // INTOLERANCES
            // ========================
            ["name" => "Lactose Intolerance",    "slug" => "lactose_intolerance",    "type" => "intolerance", "severity" => "warn"],
            ["name" => "Gluten Intolerance",     "slug" => "gluten_intolerance",     "type" => "intolerance", "severity" => "warn"],
            ["name" => "Fructose Intolerance",   "slug" => "fructose_intolerance",   "type" => "intolerance", "severity" => "warn"],
            ["name" => "Histamine Intolerance",  "slug" => "histamine_intolerance",  "type" => "intolerance", "severity" => "warn"],

            // ========================
            // MENTAL
            // ========================
            ["name" => "Depression",             "slug" => "depression",             "type" => "condition",   "severity" => "adjust"],
            ["name" => "Anxiety",                "slug" => "anxiety",                "type" => "condition",   "severity" => "adjust"],
            ["name" => "Eating Disorder",        "slug" => "eating_disorder",        "type" => "condition",   "severity" => "adjust"],

            // ========================
            // DEFICIENCIES
            // ========================
            ["name" => "Iron Deficiency Anemia", "slug" => "iron_deficiency",        "type" => "condition",   "severity" => "adjust"],
            ["name" => "Vitamin D Deficiency",   "slug" => "vitamin_d_deficiency",   "type" => "condition",   "severity" => "adjust"],

        ];

        DB::table('health_conditions')->insertOrIgnore(
            array_map(fn($c) => [...$c, 'created_at' => now(), 'updated_at' => now()], $conditions)
        );
    }
}
