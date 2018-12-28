export default (function() {

  var lengths = {
    ascii: [160, 146, 153],
    unicode: [70, 62, 66]
  };

  return {
    isUnicode: function(text) {
      for(var charPos = 0; charPos < text.length; charPos++) {
        if(text.charCodeAt(charPos) > 127 && text[charPos] != "€") {
          return true;
        }
      }
      return false;
    },

    length: function(text) {
      var smsLength = 0;

      for(var charPos = 0; charPos < text.length; charPos++) {
        switch(text[charPos]) {
          case "\n":
          case "[":
          case "]":
          case "\\":
          case "^":
          case "{":
          case "}":
          case "|":
          case "€":
            smsLength += 2;
            break;

          default:
            smsLength += 1;
        }
      }

      return smsLength;
    },

    chunkMaxLength: function(text, chunkQuantity, adaptLength) {
      var limits = (this.isUnicode(text) ? lengths.unicode : lengths.ascii);
      var limit = limits[0];
      for(var i = 1; i < chunkQuantity; i++) {
        limit += limits[Math.min(2, i)];
      }
      if(adaptLength) {
        limit -= (this.length(text) - text.length);
      }
      return limit;
    },

    chunkLengthLeft: function(text) {
      var chunks = this.chunk(text);
      var maxLength = this.chunkMaxLength(text, chunks.length);
      return maxLength - this.length(text);
    },

    chunk: function(text) {
      var chunks = [];
      var limits = (this.isUnicode(text) ? lengths.unicode : lengths.ascii);

      var limit = limits[0];
      var chunk = '';
      while(text.length) {
        if(this.length(chunk + text[0]) > limit) {
          chunks.push(chunk);
          chunk = '';
          limit = limits[Math.min(2, chunks.length)];
        }

        chunk = chunk + text[0];
        text = text.substr(1);
      }

      if(chunk) chunks.push(chunk);
      return chunks;
    }
  };
})();
