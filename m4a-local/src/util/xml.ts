export function removeDoctype(content: string) {
    return  content.replace(/<!DOCTYPE[^>]+>/, '').trim();
}