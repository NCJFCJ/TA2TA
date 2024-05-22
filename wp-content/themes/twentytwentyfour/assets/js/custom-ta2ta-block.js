const { registerBlockType } = wp.blocks;

registerBlockType( 'ta2ta/latest-documents', {
    // built-in attributes
      title: 'Latest Library Documents',
      description: 'Block of Latest documents added on the library',
      icon: 'format-image',
      category: 'layout',
  
    // cutom attributes
      attributes: {},
  
    // custom functions
  
  
    // built-in functions
  
    edit(){
      return "hello World";
    },
  
    save(){}
  });