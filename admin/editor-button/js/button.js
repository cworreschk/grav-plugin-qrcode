(function($) {
  return $(function() {
    return $('body').on('grav-editor-ready', function() {
      var Instance;
      Instance = Grav["default"].Forms.Fields.EditorField.Instance;
      return Instance.addButton({
        qrcode: {
          identifier: 'qrcode',
          title: GravAdmin.translations.PLUGIN_QRCODE.EDITOR_BUTTON_TOOLTIP,
          label: '<i class="fa fa-fw fa-qrcode"></i>',
          modes: ['gfm', 'markdown'],
          action: function(e) {
            return e.button.on('click.editor.qrcode', function() {
              var i, l, pos, posend, ref, ref1, text;
              text = prompt(GravAdmin.translations.PLUGIN_QRCODE.EDITOR_BUTTON_PROMPT);
              if (text) {
                text = "[qrcode]" + text + "[/qrcode]";
                pos = e.codemirror.getDoc().getCursor(true);
                posend = e.codemirror.getDoc().getCursor(false);
                for (l = i = ref = pos.line, ref1 = posend.line; ref <= ref1 ? i <= ref1 : i >= ref1; l = ref <= ref1 ? ++i : --i) {
                  e.codemirror.replaceRange(text + e.codemirror.getLine(l), {
                    line: l,
                    ch: 0
                  }, {
                    line: l,
                    ch: e.codemirror.getLine(l).length
                  });
                }
                e.codemirror.setCursor({
                  line: posend.line,
                  ch: e.codemirror.getLine(posend.line).length
                });
                return e.codemirror.focus();
              }
            });
          }
        }
      });
    });
  });
})(jQuery);
