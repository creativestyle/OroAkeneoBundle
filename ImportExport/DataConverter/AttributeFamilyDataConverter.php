<?php

namespace Creativestyle\Bundle\AkeneoBundle\ImportExport\DataConverter;

use Creativestyle\Bundle\AkeneoBundle\ImportExport\AkeneoIntegrationTrait;
use Creativestyle\Bundle\AkeneoBundle\Tools\AttributeFamilyCodeGenerator;
use Creativestyle\Bundle\AkeneoBundle\Tools\FieldConfigModelFieldNameGenerator;
use Creativestyle\Bundle\AkeneoBundle\Tools\Generator;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager as EntityConfigManager;
use Oro\Bundle\ImportExportBundle\Context\ContextAwareInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\LocaleBundle\ImportExport\DataConverter\LocalizedFallbackValueAwareDataConverter;
use Oro\Bundle\ProductBundle\Entity\Product;

class AttributeFamilyDataConverter extends LocalizedFallbackValueAwareDataConverter implements ContextAwareInterface
{
    use AkeneoIntegrationTrait;
    use LocalizationAwareTrait;

    /** @var array */
    protected $fieldMapping = [];

    /**
     * @var EntityConfigManager
     */
    protected $entityConfigManager;

    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /** @var ContextInterface */
    protected $context;

    public function setImportExportContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function setDoctrineHelper(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public function setEntityConfigManager(EntityConfigManager $entityConfigManager)
    {
        $this->entityConfigManager = $entityConfigManager;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToImportFormat(array $importedRecord, $skipNullValues = true)
    {
        $importedRecord['code'] = AttributeFamilyCodeGenerator::generate($importedRecord['code']);
        $importedRecord['entityClass'] = Product::class;
        $importedRecord['isEnabled'] = true;
        $importedRecord['channel:id'] = $this->context->getOption('channel');

        $this->setLabels($importedRecord);

        foreach ($importedRecord['groups'] as $key => &$group) {
            $this->setLabels($group);
            $group['akeneo_code'] = $group['code'];

            foreach ($group['attributes'] as $attributeCode) {
                if (!in_array($attributeCode, $importedRecord['attributes'])) {
                    continue;
                }

                $entityConfigFieldId = $this->entityConfigManager->getConfigModelId(
                    $importedRecord['entityClass'],
                    FieldConfigModelFieldNameGenerator::generate($attributeCode)
                );

                if ($entityConfigFieldId) {
                    $group['attributeRelations'][] = ['entityConfigFieldId' => $entityConfigFieldId];

                    continue;
                }

                // @BC: Keep Akeneo_Aken_1706289854
                $fieldName = $this->getFieldMapping()[$attributeCode] ?? null;
                if (!$fieldName) {
                    continue;
                }
                $entityConfigFieldId = $this->entityConfigManager->getConfigModelId(
                    $importedRecord['entityClass'],
                    $fieldName
                );
                if ($entityConfigFieldId) {
                    $group['attributeRelations'][] = ['entityConfigFieldId' => $entityConfigFieldId];

                    continue;
                }
            }
        }

        $importedRecord['groups'] = array_filter(
            $importedRecord['groups'],
            function ($group) {
                return !empty($group['attributeRelations']);
            }
        );

        return parent::convertToImportFormat($importedRecord, $skipNullValues);
    }

    private function getFieldMapping(): array
    {
        if ($this->fieldMapping) {
            return $this->fieldMapping;
        }

        $importExportProvider = $this->entityConfigManager->getProvider('importexport');
        foreach ($importExportProvider->getConfigs(Product::class) as $field) {
            if ('akeneo' !== $field->get('source')) {
                continue;
            }

            $source = $field->get('source_name');
            if (!$source) {
                continue;
            }

            $this->fieldMapping[$source] = $field->getId()->getFieldName();
        }

        return $this->fieldMapping;
    }

    /**
     * Set labels with locales mapping from settings.
     */
    private function setLabels(array &$importedRecord)
    {
        $labels = $importedRecord['labels'];

        $defaultLocalization = $this->getDefaultLocalization();
        $defaultLocale = $this->getTransport()->getMappedAkeneoLocale($defaultLocalization->getLanguageCode());

        $importedRecord['labels'] = [
            'default' => [
                'fallback' => null,
                'string' => $labels[$defaultLocale] ?? Generator::generateLabel($importedRecord['code']),
            ],
        ];

        $akeneoLocales = $this->getTransport()->getAkeneoLocales();
        foreach ($akeneoLocales as $akeneoLocale) {
            foreach ($this->getLocalizations($akeneoLocale->getLocale()) as $localization) {
                if (!$localization || $defaultLocalization->getLanguageCode() === $localization->getLanguageCode()) {
                    continue;
                }

                $importedRecord['labels'][$localization->getName()] = [
                    'fallback' => null,
                    'string' => $labels[$akeneoLocale->getCode()] ?? null,
                ];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getHeaderConversionRules()
    {
        return [
            'labels' => 'labels',
            'code' => 'code',
            'groups' => 'attributeGroups',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getBackendHeader()
    {
        throw new \Exception('Normalization is not implemented!');
    }
}
