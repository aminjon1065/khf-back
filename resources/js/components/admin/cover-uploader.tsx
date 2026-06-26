import { ImageIcon, Trash2, Upload } from 'lucide-react';
import { useRef, useState } from 'react';
import { Cropper, type CropperRef } from 'react-advanced-cropper';
import 'react-advanced-cropper/dist/style.css';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

export type CoverUrls = { thumb: string; card: string; hero: string; original: string };

export function CoverUploader({
    existing,
    onChange,
    aspectRatio = 16 / 9,
}: {
    existing: CoverUrls | null;
    onChange: (file: File | null) => void;
    aspectRatio?: number;
}) {
    const [preview, setPreview] = useState<string | null>(existing?.card ?? null);
    const [cropSrc, setCropSrc] = useState<string | null>(null);
    const cropperRef = useRef<CropperRef>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    const onSelect = (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }
        setCropSrc(URL.createObjectURL(file));
        event.target.value = '';
    };

    const applyCrop = () => {
        const canvas = cropperRef.current?.getCanvas();
        if (!canvas) {
            return;
        }
        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    return;
                }
                onChange(new File([blob], 'cover.jpg', { type: 'image/jpeg' }));
                setPreview(URL.createObjectURL(blob));
                setCropSrc(null);
            },
            'image/jpeg',
            0.9,
        );
    };

    const remove = () => {
        setPreview(null);
        onChange(null);
    };

    return (
        <div>
            <div
                className="relative w-full overflow-hidden rounded-lg border bg-muted"
                style={{ aspectRatio }}
            >
                {preview ? (
                    <img src={preview} alt="" className="h-full w-full object-cover" />
                ) : (
                    <div className="flex h-full w-full flex-col items-center justify-center gap-1 text-muted-foreground">
                        <ImageIcon className="size-7" />
                        <span className="text-xs">Нет обложки</span>
                    </div>
                )}
            </div>

            <div className="mt-2 flex gap-2">
                <Button type="button" variant="outline" size="sm" onClick={() => inputRef.current?.click()}>
                    <Upload className="size-4" /> {preview ? 'Заменить' : 'Загрузить'}
                </Button>
                {preview && (
                    <Button type="button" variant="ghost" size="sm" onClick={remove}>
                        <Trash2 className="size-4" /> Убрать
                    </Button>
                )}
                <input ref={inputRef} type="file" accept="image/*" className="hidden" onChange={onSelect} />
            </div>

            <Dialog
                open={cropSrc !== null}
                onOpenChange={(open) => {
                    if (!open) {
                        setCropSrc(null);
                    }
                }}
            >
                <DialogContent className="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>Кадрирование обложки</DialogTitle>
                    </DialogHeader>
                    <div className="h-[360px] overflow-hidden rounded-lg bg-black/90">
                        {cropSrc && (
                            <Cropper
                                ref={cropperRef}
                                src={cropSrc}
                                className="h-full"
                                stencilProps={{ aspectRatio }}
                            />
                        )}
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={() => setCropSrc(null)}>
                            Отмена
                        </Button>
                        <Button type="button" onClick={applyCrop}>
                            Применить
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
