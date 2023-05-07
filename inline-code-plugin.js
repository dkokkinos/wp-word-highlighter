(function () {
  function onlyUnique(value, index, array) {
    return !value.includes('[') && !value.includes('<') && array.indexOf(value) === index;
  }

  var regex;
  setTimeout(function () {
    // Get the code sections and extract their contents to a new array
    let codeSections = document.querySelectorAll('code .class-name');
    let elements = Array.from(codeSections).map(function (code) {
      return code.innerText;
    });
    if (elements.length == 0) return;
    elements = elements.filter(onlyUnique);

    regex = new RegExp("\\b(" + elements.join('|') + ")\\b", "g");
    let sections = document.querySelectorAll('.post-content p');

    sections.forEach(function (section, index) {
      addInline(section);
    });
  }, 2000);

  function addInline(element, index) {

    element.childNodes.forEach(function (element, index) {
      if (element.nodeType === 3)
        addInlineToTextNode(element);
      else {
        if (element.classList.contains('sp-important-word') || element.classList.contains('sp-inline-code'))
          return;
        addInline(element);
      }
    });

  }

  function addInlineToTextNode(node) {
    // break the node text into 3 parts: part1 - before the selected text, part2- the text to highlight, and part3 - the text after the highlight
    let s = node.nodeValue;

    let match = regex.exec(s);
    regex.lastIndex = 0;  // Reset
    if (match === null) return;
    let startIndex = match.index; // get the starting index of the match
    let endIndex = startIndex + match[0].length; // get the ending index of the match

    // get the text before the highlight
    let part1 = s.substring(0, startIndex);

    // get the text that will be highlighted
    let part2 = s.substring(startIndex, endIndex);

    // get the part after the highlight
    let part3 = s.substring(endIndex);

    // replace the text node with the new nodes
    let parentNode = node.parentNode;

    let textNode = document.createTextNode(part1);
    parentNode.replaceChild(textNode, node);

    // create a span node and add it to the parent immediately after the first text node
    let spanNode = document.createElement("span");
    spanNode.className = "sp-inline-code";
    parentNode.insertBefore(spanNode, textNode.nextSibling);

    // create a text node for the highlighted text and add it to the span node
    textNode = document.createTextNode(part2);
    spanNode.appendChild(textNode);

    // create a text node for the text after the highlight and add it after the span node
    textNode = document.createTextNode(part3);
    parentNode.insertBefore(textNode, spanNode.nextSibling);

    addInlineToTextNode(textNode);
  }
})();