# Form set

This is a proof of concept. Say you need to allow users to create "form sets"
or more accurate "form element sets". Webform module provides an amazing 
user experience for creating forms, but you're already using it for various
site forms.

## What it does

This module tries to mimic what webform does allowing a new entity type called
formset to be created. You can add form elements to the formset. And the formset
is exported to configuration.

## What it doesn't do (intentionally)

No support for submissions. Some other module might be using these form
element bundles, for whatever. This removes a lot of complexity.

## Code strategy

The strategy for re-use of the webform code, these are in rough order of priority.

### Avoid creating code

Re-using webform code where possible. Take this route that passed a Formset entity
into WebformUiElementAddForm. The code so far has no FormsetElement* classes.
 
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

The code is structure almost identically to webform (and webform UI for the elements)
so you can compare the overridden class in the same directory in webform module.
Also the admin paths are the same.

This is not so much a technical limitation as a developer convenience trying to work
out what's going on.

### Wrap webform classes

As an example, in many cases the Entity plugin architecture expects certain features.
`FormsetEntityViewBuilder` is unecessary as a Formset has no route or need to be "viewed"
but having a thin wrapper around `WebformEntityViewBuilder` keeps things transparent
and Drupal happy.

### Re-use methods, and tweak

If a class method needs to be overridden, try calling the webform parent class
and then modify the response from the parent.

As an exaple, the class `FormsetEntityListBuilder` which extends `WebformEntityListBuilder`
calls `->getDefaultOperations` on the parent and then removes the submission related
operations.

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

Site builders can't use this code. I've barely tested the elements other than "number"
and "text". Missing features like can't delete elements. The module does nothing
by itself really - you'd need custom code to use the "formsets".

Developers could get some value if you understand the code you could potentially
use it for something. It's pretty coupled with Webform and I'm making the assumption
that Webform is fairly stable. Your call.

## Conclusion

Judging by how (relatively) easy this module was to write, is a big thumbs up for
the Webform module's maturity, I wasn't expecting to be able to re-use
the `webform/modules/webform_ui/src/Form/` classes almost completely - aside from a
little route jiggery and one method override in `WebformCodeMirror`.

I think a module would be useful for developers who need to put some form building
in the hands of content editors. It's architecturally achievable, but such a module
would represent an ... unusual ... downstream consumer of Webform code.
