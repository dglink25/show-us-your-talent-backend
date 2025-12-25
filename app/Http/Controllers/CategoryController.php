<?php
// app/Http/Controllers/CategoryController.php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategoriesByEdition($editionId)
    {
        try {
            $categories = Category::where('edition_id', $editionId)
                ->where('active', true)
                ->orderBy('ordre_affichage')
                ->get(['id', 'nom', 'description', 'edition_id', 'ordre_affichage', 'active']);
            
            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Catégories récupérées avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des catégories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}