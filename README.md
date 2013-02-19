CodeIgniter Static Loader
=========================

CodeIgniter Static Loader Library for projects using HMVC and YUI3.

```php
<?php

// Loads StaticLoader Library.
$this->load->library("static_loader");

// Loads modules you need.
$this->static_loader->set(
    "tv/channel/channel",
    "tv/channel/_channel_welcome",
    "tv/channel/_channel_support_button",
    "tv/channel/_channel_loading",
    "tv/channel/_channel_info",
    "tv/channel/_channel_player",
    "tv/channel/_channel_playlist",
); 

// Output the HTML code.
echo $this->static_loader->load();
?>
```
