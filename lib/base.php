<!DOCTYPE html>

<html>

<head>

<script>
function addRow(name, url, isdir, size, date_modified) {
  if (name == ".")
    return;

  var root = "" + document.location;
  if (root.substr(-1) !== "/")
    root += "/";

  var table = document.getElementById("table");
  var row = document.createElement("tr");
  var file_cell = document.createElement("td");
  var link = document.createElement("a");

  link.className = isdir ? "icon dir" : "icon file";

  if (name == "..") {
    link.href = root + "..";
    link.innerText = document.getElementById("parentDirText").innerText;
    link.className = "icon up";
    size = "";
    date_modified = "";
  } else {
    if (isdir) {
      name = name + "/";
      url = url;
      size = "";
    } else {
      link.draggable = "true";
      link.addEventListener("dragstart", onDragStart, false);
    }
    link.innerText = name;
    link.href =  url;
  }
  file_cell.appendChild(link);

  row.appendChild(file_cell);
  row.appendChild(createCell(size));
  row.appendChild(createCell(date_modified));

  table.appendChild(row);
}

function onDragStart(e) {
  var el = e.srcElement;
  var name = el.innerText.replace(":", "");
  var download_url_data = "application/octet-stream:" + name + ":" + el.href;
  e.dataTransfer.setData("DownloadURL", download_url_data);
  e.dataTransfer.effectAllowed = "copy";
}

function createCell(text) {
  var cell = document.createElement("td");
  cell.setAttribute("class", "detailsColumn");
  cell.innerText = text;
  return cell;
}

function start(location) {
  var header = document.getElementById("header");
  header.innerText = header.innerText.replace("LOCATION", location);

  document.getElementById("title").innerText = header.innerText;
}

function onListingParsingError() {
  var box = document.getElementById("listingParsingErrorBox");
  box.innerHTML = box.innerHTML.replace("LOCATION", encodeURI(document.location)
      + "?raw");
  box.style.display = "block";
}
</script>

<style>


.logo {
  -webkit-appearance: none;
  border: 0;
  background: url("lib/image/data.png") no-repeat 50% 50%;
  width: 80px;
  height: 80px;
  cursor: pointer;
  margin: 10px;
  float: left;
}

  h1 {
    border-bottom: 1px solid #c0c0c0;
    margin-bottom: 10px;
    padding-bottom: 10px;
    white-space: nowrap;
  }

  table {
    border-collapse: collapse;
  }

  tr.header {
    font-weight: bold;
  }

  td.detailsColumn {
    padding-left: 2em;
    text-align: right;
    white-space: nowrap;
  }

  a.icon {
    padding-left: 1.5em;
    margin-bottom: 0.5em;
    font-size:1.4em;
    width:24px;
    height:24px;
    text-decoration: none;
  }

  a.icon:hover {
    text-decoration: underline;
  }

  a.file {
 
  }

  a.up {
 
  }

  a.dir {
 

  }

  #listingParsingErrorBox {
    border: 1px solid black;
    background: #fae691;
    padding: 20px;
    display: none;
  }
</style>

<title id="title"></title>

</head>

<body>

<div class="header">
  <button class="logo" tabindex=3></button>
  <form onsubmit="setSearch(this.term.value); return false;"
      class="form">
    <input type="search" id="term" tabindex=1 autofocus incremental
        i18n-values="placeholder:search_button">
  </form>
</div>

<div id="listingParsingErrorBox" i18n-values=".innerHTML:listingParsingErrorBoxText"></div>

<span id="parentDirText" style="display:none" i18n-content="parentDirText"></span>
<table cellpadding='3'>
  <tbody>
    <tr>
      <td>
        <a class="icon file" target="new" href=""></a>
      </td>
     <td>
        <a class="icon file" target="new" href=""></a>
      </td>
     <td>
        <a class="icon file" target="new" href=""></a>
      </td>

    </tr>
  </tbody>
</table>
<h1 id="header" i18n-content="header"></h1>

<table id="table">
  <tr class="header">
    <td i18n-content="headerContract"></td>
    <td class="detailsColumn" i18n-content="headerPL-CUM"></td>
    <td class="detailsColumn" i18n-content="headerDateModified"></td>
    <td>
</td>
  </tr>
</table>

</body>

</html>
<script>var templateData = {"header":"Index of LOCATION","headerDateModified":"Date Modified","headerContract":"Contract","headerPL-CUM":"PL-CUM","listingParsingErrorBoxText":"Oh, no! ","parentDirText":"[parent directory]"};</script><script>// Copyright (c) 2010 The Chromium Authors. All rights reserved.
// Use of this source code is governed by a BSD-style license that can be
// found in the LICENSE file.

/**
 * @fileoverview This is a simple template engine inspired by JsTemplates
 * optimized for i18n.
 *
 * It currently supports two handlers:
 *
 *   * i18n-content which sets the textContent of the element
 *
 *     <span i18n-content="myContent"></span>
 *     i18nTemplate.process(element, {'myContent': 'Content'});
 *
 *   * i18n-values is a list of attribute-value or property-value pairs.
 *     Properties are prefixed with a '.' and can contain nested properties.
 *
 *     <span i18n-values="title:myTitle;.style.fontPL-CUM:fontPL-CUM"></span>
 *     i18nTemplate.process(element, {
 *       'myTitle': 'Title',
 *       'fontPL-CUM': '13px'
 *     });
 */

var i18nTemplate = (function() {
  /**
   * This provides the handlers for the templating engine. The key is used as
   * the attribute name and the value is the function that gets called for every
   * single node that has this attribute.
   * @type {Object}
   */
  var handlers = {
    /**
     * This handler sets the textContent of the element.
     */
    'i18n-content': function(element, attributeValue, obj) {
      element.textContent = obj[attributeValue];
    },

    /**
     * This handler adds options to a select element.
     */
    'i18n-options': function(element, attributeValue, obj) {
      var options = obj[attributeValue];
      options.forEach(function(values) {
        var option = typeof values == 'string' ? new Option(values) :
            new Option(values[1], values[0]);
        element.appendChild(option);
      });
    },

    /**
     * This is used to set HTML attributes and DOM properties,. The syntax is:
     *   attributename:key;
     *   .domProperty:key;
     *   .nested.dom.property:key
     */
    'i18n-values': function(element, attributeValue, obj) {
      var parts = attributeValue.replace(/\s/g, '').split(/;/);
      for (var j = 0; j < parts.length; j++) {
        var a = parts[j].match(/^([^:]+):(.+)$/);
        if (a) {
          var propName = a[1];
          var propExpr = a[2];

          // Ignore missing properties
          if (propExpr in obj) {
            var value = obj[propExpr];
            if (propName.charAt(0) == '.') {
              var path = propName.slice(1).split('.');
              var object = element;
              while (object && path.length > 1) {
                object = object[path.shift()];
              }
              if (object) {
                object[path] = value;
                // In case we set innerHTML (ignoring others) we need to
                // recursively check the content
                if (path == 'innerHTML') {
                  process(element, obj);
                }
              }
            } else {
              element.setAttribute(propName, value);
            }
          } else {
            console.warn('i18n-values: Missing value for "' + propExpr + '"');
          }
        }
      }
    }
  };

  var attributeNames = [];
  for (var key in handlers) {
    attributeNames.push(key);
  }
  var selector = '[' + attributeNames.join('],[') + ']';

  /**
   * Processes a DOM tree with the {@code obj} map.
   */
  function process(node, obj) {
    var elements = node.querySelectorAll(selector);
    for (var element, i = 0; element = elements[i]; i++) {
      for (var j = 0; j < attributeNames.length; j++) {
        var name = attributeNames[j];
        var att = element.getAttribute(name);
        if (att != null) {
          handlers[name](element, att, obj);
        }
      }
    }
  }

  return {
    process: process
  };
})();
</script><script>
// Invoke the template engine previously loaded from i18n_template.js
i18nTemplate.process(document, templateData);
</script><script>start("<<Finance::Quant>>Latest Analytics");</script>


<?php 

foreach(array("AAPL","GOOG") as $k => $v){
  printf('<script>addRow("%s","http://html5stockbot.com/data/documentation/ibes.html",1,"4.0 kB","2/12/12 5:33:06 AM");</script>',$v);
}



?>
<table width="100%">
<tr>
<td>
<iframe frameborder="0" src="../banner/index.php" style="height: 5000px; width: 99%"></iframe>
</td>
<td>
</td>
</tr>
</table>
<!--script>addRow("..","..",1,"36.0 kB","2/16/12 12:36:26 PM");</script>
<script>addRow("datax","datax",1,"4.0 kB","2/12/12 5:33:06 AM");</script>
<script>addRow("sas","sas",1,"4.0 kB","2/3/12 3:38:39 AM");</script>
<script>addRow("stuff","stuff",1,"12.0 kB","2/14/12 10:47:35 PM");</script>
<script>addRow("test","test",1,"28.0 kB","2/16/12 11:45:26 AM");</script>
<script>addRow("@TITLE@.html","@TITLE@.html",0,"2.5 kB","2/16/12 1:14:46 PM");</script>
<script>addRow("andromeda.png","andromeda.png",0,"1.0 MB","2/16/12 4:54:13 AM");</script>
<script>addRow("arabic.html","arabic.html",0,"574 B","2/16/12 1:15:26 PM");</script>
<script>addRow("arabic.txt","arabic.txt",0,"305 B","2/16/12 1:15:23 PM");</script>
<script>addRow("chart-production.png","chart-production.png",0,"424 kB","2/16/12 3:32:05 AM");</script>
<script>addRow("deport.pl","deport.pl",0,"18.7 kB","2/16/12 9:21:25 AM");</script>
<script>addRow("MMYT.html","MMYT.html",0,"577 B","2/16/12 1:21:46 PM");</script>
<script>addRow("MMYT.txt","MMYT.txt",0,"305 B","2/16/12 1:16:34 PM");</script>
<script>addRow("OrderMaker.pm","OrderMaker.pm",0,"3.8 kB","2/16/12 8:37:40 AM");</script>
<script>addRow("rss-2.0-sample-from-rssboard-multiple-skip-days-and-hours.xml","rss-2.0-sample-from-rssboard-multiple-skip-days-and-hours.xml",0,"3.8 kB","2/16/12 9:34:15 AM");</script>
<script>addRow("rss2html.pl","rss2html.pl",0,"2.6 kB","2/16/12 9:34:43 AM");</script>
<script>addRow("tutor.nb","tutor.nb",0,"33.8 kB","2/16/12 10:01:00 AM");</script>
<script>addRow("vim-stylesheet.css","vim-stylesheet.css",0,"756 B","2/16/12 1:21:46 PM");</script>
<script>addRow("vim2html.pl","vim2html.pl",0,"4.4 kB","2/16/12 1:21:44 PM");</script//-->

