import { Head, router } from '@inertiajs/react';
import { File, Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

type Item = {
    id: number;
    name: string;
    file_name: string;
    mime: string | null;
    size: string;
    url: string;
    preview: string | null;
    collection: string;
    model: string;
    created: string | null;
};

export default function MediaIndex({ items }: { items: Item[] }) {
    const remove = (item: Item) => {
        if (confirm(`Удалить «${item.file_name}»?`)) {
            router.delete(`/admin/media/${item.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Медиа" />

            <div className="flex items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Медиа</h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Всего файлов: {items.length.toLocaleString('ru-RU')}
                    </p>
                </div>
            </div>

            {items.length === 0 ? (
                <div className="mt-6 rounded-xl border p-8 text-center text-muted-foreground">Медиа пока нет</div>
            ) : (
                <div className="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    {items.map((item) => (
                        <Card key={item.id} className="gap-0 overflow-hidden py-0">
                            <div className="flex aspect-video items-center justify-center bg-muted">
                                {item.preview ? (
                                    <img src={item.preview} alt={item.file_name} className="size-full object-cover" />
                                ) : (
                                    <File className="size-10 text-muted-foreground" />
                                )}
                            </div>
                            <div className="flex flex-col gap-1 p-4">
                                <p className="truncate text-sm font-medium" title={item.file_name}>
                                    {item.file_name}
                                </p>
                                <p className="truncate text-xs text-muted-foreground">
                                    {item.model} · {item.collection}
                                </p>
                                <div className="mt-1 flex items-center justify-between gap-2">
                                    <span className="text-xs text-muted-foreground">
                                        {item.size}
                                        {item.created ? ` · ${item.created}` : ''}
                                    </span>
                                    <Button
                                        size="icon"
                                        variant="ghost"
                                        className="size-8"
                                        onClick={() => remove(item)}
                                        aria-label="Удалить"
                                    >
                                        <Trash2 className="size-4 text-destructive" />
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    ))}
                </div>
            )}
        </>
    );
}
