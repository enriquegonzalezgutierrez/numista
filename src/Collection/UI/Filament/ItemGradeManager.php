<?php

// src/Collection/UI/Filament/ItemGradeManager.php

namespace Numista\Collection\UI\Filament;

// Import all grade classes
use Numista\Collection\UI\Filament\ItemGrades\AuGrade;
use Numista\Collection\UI\Filament\ItemGrades\FGrade;
use Numista\Collection\UI\Filament\ItemGrades\GGrade;
use Numista\Collection\UI\Filament\ItemGrades\UncGrade;
use Numista\Collection\UI\Filament\ItemGrades\VfGrade;
use Numista\Collection\UI\Filament\ItemGrades\XfGrade;

class ItemGradeManager
{
    /**
     * The single source of truth for all available item grades.
     *
     * @var array<string, class-string>
     */
    protected array $grades = [
        'unc' => UncGrade::class, // Uncirculated
        'au' => AuGrade::class,    // About Uncirculated
        'xf' => XfGrade::class,    // Extremely Fine
        'vf' => VfGrade::class,    // Very Fine
        'f' => FGrade::class,     // Fine
        'g' => GGrade::class,      // Good
    ];

    /**
     * Dynamically generates the list of grades for a Select field.
     */
    public function getGradesForSelect(): array
    {
        $gradeKeys = array_keys($this->grades);
        $translatedGrades = [];
        foreach ($gradeKeys as $gradeKey) {
            $translatedGrades[$gradeKey] = __('item.grade_'.$gradeKey);
        }

        return $translatedGrades;
    }
}
