<?php

declare(strict_types = 1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    /* Define the PHP version
     * https://github.com/rectorphp/rector#provide-php-version
     */
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_74);

    $services = $containerConfigurator->services();

    // List of rules that will be applied without asking confirmation of the user
    // Full list is here : https://github.com/rectorphp/rector/blob/master/docs/rector_rules_overview.md
    $services->set(\Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector::class);
    $services->set(\Rector\CodeQuality\Rector\Concat\JoinStringConcatRector::class);
    $services->set(\Rector\CodeQuality\Rector\FuncCall\IntvalToTypeCastRector::class);
    $services->set(\Rector\CodeQuality\Rector\FuncCall\RemoveSoleValueSprintfRector::class);
    $services->set(\Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector::class);
    $services->set(\Rector\CodeQuality\Rector\If_\CombineIfRector::class);
    $services->set(\Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class);
    $services->set(\Rector\CodeQuality\Rector\If_\ShortenElseIfRector::class);
    $services->set(\Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector::class);
    $services->set(\Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector::class);
    $services->set(\Rector\CodeQuality\Rector\LogicalAnd\LogicalToBooleanRector::class);
    $services->set(\Rector\CodeQuality\Rector\New_\NewStaticToNewSelfRector::class);
    $services->set(\Rector\CodeQuality\Rector\PropertyFetch\ExplicitMethodCallOverMagicGetSetRector::class);
    $services->set(\Rector\CodeQuality\Rector\Return_\SimplifyUselessVariableRector::class);
    $services->set(\Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector::class);
    $services->set(\Rector\CodingStyle\Rector\Class_\AddArrayDefaultToArrayPropertyRector::class);
    $services->set(\Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector::class);
    $services->set(\Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector::class);
    $services->set(\Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector::class);
    $services->set(\Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector::class);
    $services->set(\Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector::class);
    $services->set(\Rector\CodingStyle\Rector\If_\NullableCompareToNullRector::class);
    $services->set(\Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector::class);
    $services->set(\Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector::class);
    $services->set(\Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector::class);
    // $services->set(\Rector\CodingStyle\Rector\Use_\RemoveUnusedAliasRector::class);
    $services->set(\Rector\DeadCode\Rector\Array_\RemoveDuplicatedArrayKeyRector::class);
    $services->set(\Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector::class);
    $services->set(\Rector\DeadCode\Rector\Cast\RecastingRemovalRector::class);
    $services->set(\Rector\DeadCode\Rector\Cast\RecastingRemovalRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveDelegatingParentCallRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector::class);
    $services->set(\Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector::class);
    $services->set(\Rector\DeadCode\Rector\For_\RemoveDeadIfForeachForRector::class);
    $services->set(\Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector::class);
    $services->set(\Rector\DeadCode\Rector\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector::class);
    $services->set(\Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector::class);
    $services->set(\Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector::class);
    $services->set(\Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector::class);
    $services->set(\Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector::class);
    $services->set(\Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector::class);
    $services->set(\Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector::class);
    $services->set(\Rector\Order\Rector\Class_\OrderPrivateMethodsByUseRector::class);
    $services->set(\Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class);
    $services->set(\Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector::class);
    $services->set(\Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector::class);
    $services->set(\Rector\Php71\Rector\List_\ListToArrayDestructRector::class);
    $services->set(\Rector\Php72\Rector\FuncCall\GetClassOnNullRector::class);
    $services->set(\Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class);
    $services->set(\Rector\Php74\Rector\Assign\NullCoalescingOperatorRector::class);
    $services->set(\Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector::class);
    $services->set(\Rector\Php74\Rector\Property\TypedPropertyRector::class);
    $services->set(\Rector\Privatization\Rector\Class_\ChangeReadOnlyVariableWithDefaultValueToConstantRector::class);
    $services->set(\Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector::class);
    $services->set(\Rector\Privatization\Rector\Property\ChangeReadOnlyPropertyWithDefaultValueToConstantRector::class);
    $services->set(\Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\AddArrayParamDocTypeRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\AddArrayReturnDocTypeRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByParentCallTypeRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\FunctionLike\ParamTypeDeclarationRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\Param\ParamTypeFromStrictTypedPropertyRector::class);
    $services->set(\Rector\TypeDeclaration\Rector\Property\PropertyTypeDeclarationRector::class);
    //$services->set(\Rector\CodeQuality\Rector\Name\FixClassCaseSensitivityNameRector::class);

    /* Autoloading
     * https://github.com/rectorphp/rector#extra-autoloading
     */
    if (is_file(getcwd() . '/vendor/autoload.php')) {
        $parameters->set(
            Option::AUTOLOAD_PATHS,
            [
                getcwd() . '/vendor/autoload.php',
            ]
        );
    }

    /* Don't auto import names
     * https://github.com/rectorphp/rector#import-use-statements
     */
    $parameters->set(Option::AUTO_IMPORT_NAMES, false);

    /* List of paths to scan and refactor
     * https://github.com/rectorphp/rector#paths
     */
    $parameters->set(
        Option::PATHS,
        [
            getcwd() . '/app',
        ]
    );

    /* Paths Exclusions
     * https://github.com/rectorphp/rector#exclude-paths-and-rectors
     */
    $parameters->set(
        Option::SKIP,
        [
            getcwd() . '/.cache/*',
            getcwd() . '/.config/*',
            getcwd() . '/.output/*',
            getcwd() . '/.vscode/*',
            getcwd() . '/vendor/*',
        ]
    );
};
