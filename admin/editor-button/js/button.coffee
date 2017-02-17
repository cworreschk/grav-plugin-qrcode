(($) ->
  $ ->
    $('body').on 'grav-editor-ready', ->
      Instance = Grav.default.Forms.Fields.EditorField.Instance

      Instance.addButton qrcode:
        identifier: 'qrcode'
        title: GravQrCodePlugin.translations.EDITOR_BUTTON_TOOLTIP
        label: '<i class="fa fa-fw fa-qrcode"></i>'
        modes: [
          'gfm',
          'markdown'
        ]
        action: (e) ->
          e.button.on 'click.editor.qrcode', ->
            text = prompt(GravQrCodePlugin.translations.EDITOR_BUTTON_PROMPT);
            if text
              text = "[qrcode]#{text}[/qrcode]";

              pos    = e.codemirror.getDoc().getCursor(true);
              posend = e.codemirror.getDoc().getCursor(false);

              for l in [pos.line..posend.line]
                e.codemirror.replaceRange( text + e.codemirror.getLine(l), { line: l, ch: 0 }, { line: l, ch: e.codemirror.getLine(l).length })

              e.codemirror.setCursor({ line: posend.line, ch: e.codemirror.getLine(posend.line).length });
              e.codemirror.focus();
              return
          return
      return
    return
  return
) jQuery
