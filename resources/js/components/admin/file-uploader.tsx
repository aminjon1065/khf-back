import { FileText, Upload } from 'lucide-react';
import { useRef, useState } from 'react';
import { Button } from '@/components/ui/button';

/** Загрузка произвольного файла (документы) — без кадрирования. */
export function FileUploader({
    existingName,
    existingUrl,
    onChange,
}: {
    existingName?: string | null;
    existingUrl?: string | null;
    onChange: (file: File | null) => void;
}) {
    const [name, setName] = useState<string | null>(existingName ?? null);
    const inputRef = useRef<HTMLInputElement>(null);

    const onSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0] ?? null;
        setName(file?.name ?? existingName ?? null);
        onChange(file);
    };

    return (
        <div className="flex items-center gap-2">
            <input ref={inputRef} type="file" className="hidden" onChange={onSelect} />
            <Button type="button" variant="outline" size="sm" onClick={() => inputRef.current?.click()}>
                <Upload className="size-4" /> {name ? 'Заменить файл' : 'Загрузить файл'}
            </Button>
            {name && (
                <span className="flex items-center gap-1.5 truncate text-sm text-muted-foreground">
                    <FileText className="size-4 flex-none" />
                    {existingUrl ? (
                        <a href={existingUrl} target="_blank" rel="noreferrer" className="truncate hover:text-foreground">
                            {name}
                        </a>
                    ) : (
                        <span className="truncate">{name}</span>
                    )}
                </span>
            )}
        </div>
    );
}
