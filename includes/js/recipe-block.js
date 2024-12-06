const { registerBlockType } = wp.blocks;
const { RichText } = wp.blockEditor;

registerBlockType('familyfolio/recipe', {
    title: 'Recipe',
    icon: 'carrot',
    category: 'common',
    attributes: {
        title: { type: 'string', source: 'html', selector: 'h2' },
        ingredients: { type: 'string', source: 'html', selector: 'ul' },
        steps: { type: 'string', source: 'html', selector: 'ol' },
    },
    edit: ({ attributes, setAttributes }) => (
        <div>
            <RichText
                tagName="h2"
                placeholder="Recipe Title"
                value={attributes.title}
                onChange={(value) => setAttributes({ title: value })}
            />
            <RichText
                tagName="ul"
                placeholder="Ingredients"
                value={attributes.ingredients}
                onChange={(value) => setAttributes({ ingredients: value })}
            />
            <RichText
                tagName="ol"
                placeholder="Steps"
                value={attributes.steps}
                onChange={(value) => setAttributes({ steps: value })}
            />
        </div>
    ),
    save: ({ attributes }) => (
        <div>
            <RichText.Content tagName="h2" value={attributes.title} />
            <RichText.Content tagName="ul" value={attributes.ingredients} />
            <RichText.Content tagName="ol" value={attributes.steps} />
        </div>
    ),
});
