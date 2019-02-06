# Form set

Proof of concept. Do not use in production.

## What it does

This module tries to mimic a subset of what Webform does by allowing a new entity type called
`formset` to be created. A formset is like kissing cousin to `webform` (yes it's a little coupled).

You can create a `formset` entity, and at its core it's a super dumbed down webform. Then you 
can add form elements to the "formset". And the formsets are exported to configuration
just like webform.

## Why

This is a proof of concept. Say you need to allow users to create "form sets"
or more accurate "form element sets". Webform module provides an amazing 
user experience for creating forms, but you're already using it for various
site forms. So you use this module to give users somewhere to create form sets.

## What it doesn't do (intentionally)

No support for submissions. Some other module might be using these form
element bundles, for whatever. This removes a lot of complexity.

No "view" of a formset. The idea is you programmatically load it like you would a
webform, for other purposes.

Very minimal consideration around editing access. Structurally it has the same
access class as Webform, but dumbed down (an not tested).

## Code strategy

The strategy for re-use of the webform code, these are in rough order of priority.

### Avoid creating code

Re-using webform code where possible. For example, this route passes a Formset entity
into WebformUiElementAddForm (handling an awkward aspect of how route arguments are 
passed into form builders).
 
```
entity.formset.element.add_form:
  path: '/admin/structure/formset/manage/{webform}/element/add/{type}'
  defaults:
    _form: '\Drupal\webform_ui\Form\WebformUiElementAddForm'
    _title: 'Add element'
  requirements:
    _custom_access: '\Drupal\formset\Access\FormsetAccountAccess::checkEditAccess'
  options:
    parameters:
      webform:
        type: 'entity:formset'
``` 
 
### Use webform structure

The code is structured almost identically to webform and webform_ui,
so you can compare the overridden classes in the same directory between each module.
The admin paths are the same for the same reason.

This is not so much a technical limitation as a developer convenience trying to work
out what's going on.

### Wrap webform classes

As an example, in many cases the Entity plugin architecture expects certain features.
`FormsetEntityViewBuilder` isn't needed for our module but having it keeps Drupal
happy. So a thin wrapper around `WebformEntityViewBuilder` is used 

### Re-use methods, and tweak

If a class method needs to be overridden, we try calling the webform parent class
and then modify the response from the parent.

As an example, the class `FormsetEntityListBuilder` (which extends
`WebformEntityListBuilder`) calls `getDefaultOperations()` on the parent and then
removes the submission related operations.

```
  public function getDefaultOperations(EntityInterface $entity, $type = 'edit') {
    $operations = parent::getDefaultOperations($entity, $type = 'edit');
    unset($operations['submission_page']);
    unset($operations['submission_view_any']);
    unset($operations['results']);
    return $operations;
  }
```

### Fake it

In many cases a method on Formset overrides a Webform method simply so the code
won't complain. For exammle `FormsetEntityStorage` pretends it has zero submissions
avoiding any webform complications.

```
  /**
   * Not relevent for form sets.
   */
  public function getTotalNumberOfResults($formset_id = NULL) {
    return 0;
  }
```

## Can you use this code?

 *Do not use on production website.*
 
 It's not security tested, only cursory treatment of permissions.

Site builders can't use this code. I've barely tested the elements other than "number"
and "text". There are important features missing, like deleting elements.

Also the module does nothing by itself really - you'd need custom code to use the "formsets".

Developers could get some value if you understand the code you could potentially
build on it. Just remember that it'ss pretty coupled with Webform and there is an
assumption that Webform is fairly stable. Your call.

## Conclusion

Judging by how (relatively) easy this module was to write, it is a big thumbs up for
the Webform module's maturity. I wasn't expecting to be able to re-use
the classes in `webform/modules/webform_ui/src/Form/` classes almost completely.

I think a module like this would be useful for developers who need to put some form building
in the hands of content editors. It's architecturally achievable, but such a module
would represent a downstream consumer of Webform code which would frequently raise 
questions like "is this change to the element ui a breaking change to formset
module," which might not be an ideal scenario. But Webform is robust, not a moving
target like some other modules.
