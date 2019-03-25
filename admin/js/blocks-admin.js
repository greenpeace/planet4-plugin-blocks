/*
  global $,
    _,
    Select2FieldsSetup,
    P4FieldsSetup,
    WPShortcakeHooksSetup,
    ArticlesBlock,
    ColumnsBlock,
    NewCoversBlock,
    CarouselHeaderBlock,
    SocialMediaBlock,
    SubmenuBlock
*/

// Define a p4_blocks object that holds functions used during rendering backend blocks' views.
function P4BlocksAdminUI(Select2FieldsSetup, P4FieldsSetup, WPShortcakeHooksSetup, blocksMap) {
  var me = this;

  me.hooks_defined = false;

  me.setupSelect2Fields = Select2FieldsSetup;
  me.setupP4Fields = P4FieldsSetup;
  me.setupWPShortcakeHooks = WPShortcakeHooksSetup;
  me.blocksMap = blocksMap;

  me.blocks = {};

  me.initialize_view_fields = function (block_name) {
    switch (block_name) {
    case 'articles':
      me.blocks['ArticlesBlock'].initialize_view_fields();
      break;
    case 'newcovers':
      me.blocks['NewCoversBlock'].initialize_view_fields();
      break;
    }
  };

  me.find_view = function (collection, name) {
    return _.find(
      collection,
      function (viewModel) {
        return name === viewModel.model.get('attr');
      }
    );
  };

  /**
   * Call a block's constructor and inject this class down.
   */
  me.setupBlock = function(blockClass) {
    me.blocks[blockClass] = new me.blocksMap[blockClass](me);
  };

  /**
   * Initialize common components
   */
  me.setup = function() {
    me.setupSelect2Fields();
    me.setupP4Fields();
    me.setupWPShortcakeHooks(me);

    for (var block in me.blocksMap) {
      me.setupBlock(block);
    }
  };
}

jQuery(function () {
  'use strict';

  var blocksMap = {
    'ArticlesBlock'       : ArticlesBlock,
    'CarouselHeaderBlock' : CarouselHeaderBlock,
    'ColumnsBlock'        : ColumnsBlock,
    'NewCoversBlock'      : NewCoversBlock,
    'SocialMediaBlock'    : SocialMediaBlock,
    'SubmenuBlock'        : SubmenuBlock,
  };

  var p4BlocksUI = new P4BlocksAdminUI(Select2FieldsSetup, P4FieldsSetup, WPShortcakeHooksSetup, blocksMap);
  p4BlocksUI.setup();
});
