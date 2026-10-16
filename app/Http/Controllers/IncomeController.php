<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function create(Request $request): View
    {
        $categories = $this->categoriesFor($request->user()->id);

        $envelopes = $categories
            ->where('is_system', false)
            ->map(fn ($c) => ['name' => $c->name, 'percentage' => (float) $c->percentage])
            ->values();

        return view('incomes.create', compact('envelopes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['required', 'date'],
        ]);

        $categories = $this->categoriesFor($userId);
        $system = $categories->firstWhere('is_system', true);

        DB::transaction(function () use ($data, $categories, $system, $userId) {
            $groupId = (string) Str::uuid();
            $total = (int) $data['amount'];
            $allocated = 0;

            foreach ($categories->where('is_system', false) as $category) {
                $share = (int) floor($total * (float) $category->percentage / 100);

                if ($share <= 0) {
                    continue;
                }

                $this->addIncome($userId, $category, $share, $data, $groupId);
                $allocated += $share;
            }

            // Sisa persentase dan pecahan pembulatan masuk ke kategori Sisa
            $rest = $total - $allocated;

            if ($rest > 0) {
                $this->addIncome($userId, $system, $rest, $data, $groupId);
            }
        });

        return redirect()->route('categories.index');
    }

    private function categoriesFor(int $userId)
    {
        Category::firstOrCreate(
            ['user_id' => $userId, 'is_system' => true],
            ['name' => 'Sisa', 'percentage' => 0, 'balance' => 0]
        );

        return Category::where('user_id', $userId)
            ->orderBy('is_system')
            ->orderBy('name')
            ->get();
    }

    private function addIncome(int $userId, Category $category, int $amount, array $data, string $groupId): void
    {
        Transaction::create([
            'user_id' => $userId,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => $amount,
            'note' => $data['note'] ?? null,
            'transaction_date' => $data['transaction_date'],
            'group_id' => $groupId,
        ]);

        $category->increment('balance', $amount);
    }
}
