import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import { EditorContent, useEditor, type Editor } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import {
    Bold,
    Heading2,
    Heading3,
    Italic,
    Link as LinkIcon,
    List,
    ListOrdered,
    Quote,
    Redo,
    Strikethrough,
    Undo,
} from 'lucide-react';
import { cn } from '@/lib/utils';

function Btn({
    onClick,
    active,
    disabled,
    title,
    children,
}: {
    onClick: () => void;
    active?: boolean;
    disabled?: boolean;
    title: string;
    children: React.ReactNode;
}) {
    return (
        <button
            type="button"
            title={title}
            onClick={onClick}
            disabled={disabled}
            className={cn(
                'inline-flex size-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-muted hover:text-foreground disabled:opacity-40',
                active && 'bg-muted text-foreground',
            )}
        >
            {children}
        </button>
    );
}

function Toolbar({ editor }: { editor: Editor | null }) {
    if (!editor) {
        return null;
    }

    const setLink = () => {
        const previous = editor.getAttributes('link').href as string | undefined;
        const url = window.prompt('Ссылка (URL):', previous ?? '');
        if (url === null) {
            return;
        }
        if (url === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            return;
        }
        editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    };

    return (
        <div className="flex flex-wrap items-center gap-0.5 border-b p-1.5">
            <Btn title="Жирный" active={editor.isActive('bold')} onClick={() => editor.chain().focus().toggleBold().run()}>
                <Bold className="size-4" />
            </Btn>
            <Btn title="Курсив" active={editor.isActive('italic')} onClick={() => editor.chain().focus().toggleItalic().run()}>
                <Italic className="size-4" />
            </Btn>
            <Btn title="Зачёркнутый" active={editor.isActive('strike')} onClick={() => editor.chain().focus().toggleStrike().run()}>
                <Strikethrough className="size-4" />
            </Btn>
            <span className="mx-1 h-5 w-px bg-border" />
            <Btn title="Заголовок 2" active={editor.isActive('heading', { level: 2 })} onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}>
                <Heading2 className="size-4" />
            </Btn>
            <Btn title="Заголовок 3" active={editor.isActive('heading', { level: 3 })} onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}>
                <Heading3 className="size-4" />
            </Btn>
            <Btn title="Маркированный список" active={editor.isActive('bulletList')} onClick={() => editor.chain().focus().toggleBulletList().run()}>
                <List className="size-4" />
            </Btn>
            <Btn title="Нумерованный список" active={editor.isActive('orderedList')} onClick={() => editor.chain().focus().toggleOrderedList().run()}>
                <ListOrdered className="size-4" />
            </Btn>
            <Btn title="Цитата" active={editor.isActive('blockquote')} onClick={() => editor.chain().focus().toggleBlockquote().run()}>
                <Quote className="size-4" />
            </Btn>
            <Btn title="Ссылка" active={editor.isActive('link')} onClick={setLink}>
                <LinkIcon className="size-4" />
            </Btn>
            <span className="mx-1 h-5 w-px bg-border" />
            <Btn title="Отменить" disabled={!editor.can().undo()} onClick={() => editor.chain().focus().undo().run()}>
                <Undo className="size-4" />
            </Btn>
            <Btn title="Повторить" disabled={!editor.can().redo()} onClick={() => editor.chain().focus().redo().run()}>
                <Redo className="size-4" />
            </Btn>
        </div>
    );
}

export function RichTextEditor({
    value,
    onChange,
    placeholder,
}: {
    value: string;
    onChange: (html: string) => void;
    placeholder?: string;
}) {
    const editor = useEditor({
        extensions: [
            StarterKit,
            Image,
            Placeholder.configure({ placeholder: placeholder ?? 'Введите текст…' }),
        ],
        content: value || '',
        onUpdate: ({ editor }) => onChange(editor.getHTML()),
        editorProps: {
            attributes: { class: 'tiptap-content min-h-[220px] px-3 py-2 text-sm' },
        },
        immediatelyRender: false,
    });

    return (
        <div className="rounded-lg border bg-background">
            <Toolbar editor={editor} />
            <EditorContent editor={editor} />
        </div>
    );
}
