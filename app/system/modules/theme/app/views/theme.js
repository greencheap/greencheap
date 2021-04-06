import UIkit from "uikit";
import Icons from "../../../../../../node_modules/uikit/dist/js/uikit-icons";

// loads the Icon plugin
UIkit.use(Icons);

import "../../assets/less/theme.less";

import { $, on, css, toNodes, isString, assign, html, remove } from "uikit-util";
import Autocomplete from "../lib/autocomplete";
import Pagination from "../lib/pagination";
import HTMLEditor from "../lib/htmleditor";
