# Magento 2 FAQ Page GraphQL

## 1. Documentation

- [Contribute on Github](https://github.com/marcinmaterzok/magento2-faq-page-graphql)
- [Releases](https://github.com/marcinmaterzok/magento2-faq-page-graphql/releases)

## 2. How to install

### Install via composer (recommend)
Run the following command in Magento 2 root folder:
```
composer require mtrzk/magento2-faqpage mtrzk/magento2-faqpage-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## 3. How to use
```
Query {
    faqQuestions: [FaqQuestion!]
}

type FaqQuestion {
    id: ID!
    question: String
    answer: String
    position: String
    active: Boolean
    store_ids: [String]
    created_at: String
    updated_at: String
}

StoreConfig {
    faqpage_general_is_enabled: Boolean 
    faqpage_general_add_to_menu: String 
    faqpage_general_faq_menu_name: String 
}
```

## 4. CHANGELOG
Version 1.0.1

```
- Added README
- Fixed casting for FaqQuestion Type
```

Version 1.0.0

```
- First commit
- Added support for GraphQL
```
