metagrab
========

### Give it up, NSM Better Meta!

For a given channel entry id, grab one of the following NSM Better Meta attributes:

* title
* description
* keywords
* canonical_url

and fall back to a given default value if nothing turns up.

### In gory detail

    {exp:channel:entries}

        {exp:metagrab entry_id='{entry_id}' attribute='title' default='{title}'}

        {exp:metagrab entry_id='{entry_id}' attribute='description' default='My Enchanting Description'}

        {exp:metagrab entry_id='{entry_id}' attribute='keywords' default='foo bar'}

        {exp:metagrab entry_id='{entry_id}' attribute='canonical_url' default='{page_url}'}

    {/exp:channel:entries}

### Or maybe you'd like avoid entering a non-default rel="canonical" link for every single page on your site

    {exp:switchee variable='{structure:page:entry_id}' parse='inward'}

    {case value=''}
         <link rel="canonical" href="{current_url}">
      {/case}

      {case default='yes'}
         <link rel="canonical" href="{exp:metagrab entry_id='{structure:page:entry_id}' attribute='canonical_url' default='{current_url}'}">
      {/case}

    {/exp:switchee}

### Things not working out as expected?

Use the debug, Luke:

    {exp:metagrab entry_id='{structure:page:entry_id}' attribute='canonical_url' default='{current_url}' debug='yes'}

and watch turn on Template Debugging.
