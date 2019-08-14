Magento 2 Layout Debugger
---

## Module for quickly debugging and understanding the final rendered layout for a page

Officially supports Magento 2.2.0+

### Installation 

**Download it**
```bash
composer require nathanjosiah/magento2-layout-debugger:dev-master
```

**Register it with Magento**
```bash
bin/magento setup:upgrade
```

**_For production mode_ also run**
```bash
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
```


### Configure it

**Go to Store->Configuration->Advanced->Developer->Debug**

#### Enable Layout Debugging Dump On Storefront/Admin

Outputs a popup containing information about the rendered layout for a given page. The popup can be expanded and hidden.

![Popup](/docs/popup.png)

#### Enable Layout Debugging Inline Comments On Storefront/Admin

Will add unobtrusive comments as to not interfere with CSS selectors and rendered HTML that help you quickly see where HTML is coming from in the layout.

This will be the name of the layout element, the type of the element (block, container, ui component), and the name of the parent element 

![Popup](/docs/comments.png)
