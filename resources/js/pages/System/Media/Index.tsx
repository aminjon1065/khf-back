import React, { useRef, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { Button } from '@/components/ui/button';
import { Upload, Trash2, Link as LinkIcon, FileText, Image as ImageIcon } from 'lucide-react';

interface MediaItem {
    id: string;
    file_name: string;
    url: string;
    mime_type: string;
    size: number;
    created_at: string;
}

interface Pagination {
    data: MediaItem[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    total: number;
}

export default function Index({ media }: { media: Pagination }) {
    const [isUploading, setIsUploading] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;
        if (!files || files.length === 0) return;

        setIsUploading(true);
        const formData = new FormData();
        formData.append('file', files[0]);

        router.post(route('admin.media.store'), formData, {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => {
                setIsUploading(false);
                if (fileInputRef.current) fileInputRef.current.value = '';
            },
        });
    };

    const handleDelete = (id: string) => {
        if (confirm('Are you sure you want to delete this file?')) {
            router.delete(route('admin.media.destroy', id), {
                preserveScroll: true,
            });
        }
    };

    const copyToClipboard = (url: string) => {
        navigator.clipboard.writeText(url);
        alert('URL copied to clipboard!');
    };

    const formatSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    return (
        <AuthenticatedLayout
            title="Media Library"
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold leading-tight text-slate-800">Media Library</h2>
                    <div>
                        <input
                            type="file"
                            ref={fileInputRef}
                            onChange={handleFileUpload}
                            className="hidden"
                            accept="image/*,application/pdf,.doc,.docx"
                        />
                        <Button
                            onClick={() => fileInputRef.current?.click()}
                            disabled={isUploading}
                        >
                            <Upload className="w-4 h-4 mr-2" />
                            {isUploading ? 'Uploading...' : 'Upload File'}
                        </Button>
                    </div>
                </div>
            }
        >
            <Head title="Media Library" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-slate-200">
                            
                            {media.data.length === 0 ? (
                                <div className="text-center py-12 text-slate-500">
                                    <ImageIcon className="mx-auto h-12 w-12 text-slate-300 mb-4" />
                                    <p>No media files found.</p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                                    {media.data.map((item) => (
                                        <div key={item.id} className="relative group rounded-lg border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
                                            
                                            <div className="aspect-square bg-slate-100 flex items-center justify-center relative overflow-hidden">
                                                {item.mime_type.startsWith('image/') ? (
                                                    <img
                                                        src={item.url}
                                                        alt={item.file_name}
                                                        className="object-cover w-full h-full"
                                                    />
                                                ) : (
                                                    <FileText className="w-12 h-12 text-slate-400" />
                                                )}
                                                
                                                {/* Hover Overlay */}
                                                <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                                    <button
                                                        onClick={() => copyToClipboard(item.url)}
                                                        className="p-2 bg-white rounded-full hover:bg-slate-100 transition-colors"
                                                        title="Copy URL"
                                                    >
                                                        <LinkIcon className="w-4 h-4 text-slate-700" />
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(item.id)}
                                                        className="p-2 bg-red-500 rounded-full hover:bg-red-600 transition-colors"
                                                        title="Delete File"
                                                    >
                                                        <Trash2 className="w-4 h-4 text-white" />
                                                    </button>
                                                </div>
                                            </div>

                                            <div className="p-3">
                                                <p className="text-sm font-medium text-slate-700 truncate" title={item.file_name}>
                                                    {item.file_name}
                                                </p>
                                                <p className="text-xs text-slate-500 mt-1">
                                                    {formatSize(item.size)}
                                                </p>
                                            </div>

                                        </div>
                                    ))}
                                </div>
                            )}

                            {/* Pagination (Simple for now) */}
                            <div className="mt-8 flex justify-between items-center text-sm text-slate-600">
                                <div>Total: {media.total} files</div>
                                <div className="flex gap-2">
                                    {media.prev_page_url && (
                                        <Button variant="outline" size="sm" onClick={() => router.get(media.prev_page_url!)}>
                                            Previous
                                        </Button>
                                    )}
                                    {media.next_page_url && (
                                        <Button variant="outline" size="sm" onClick={() => router.get(media.next_page_url!)}>
                                            Next
                                        </Button>
                                    )}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
