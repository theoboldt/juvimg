<?php
/**
 * This file is part of the Juvem package.
 *
 * (c) Erik Theoboldt <erik@theoboldt.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Tests;


use App\Juvimg\Image;
use App\Juvimg\ResizeImageRequest;
use App\Service\Resizer\ImagineResizeService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class ImageTest extends TestCase
{

    /**
     * Test mime type access
     */
    public function testPngImageMimeType(): void
    {
        $image = new Image(self::provideImageInput());
        $this->assertEquals('image/png', $image->getMimeType());
    }

    /**
     * Provide 16x16 px PNG file
     *
     * @return string
     */
    public static function provideImageInput(): string
    {
        $image = <<<PNG
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MC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIgogICAgICAgICAgICB4bWxuczphdXg9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvYXV4LyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyI+CiAgICAgICAgIDxleGlmRVg6TGVuc1NwZWNpZmljYXRpb24+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPjE4LzE8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT4xOC8xPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGk+NTEyNjMvMTQ2MzQ8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT41MTI2My8xNDYzNDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwvZXhpZkVYOkxlbnNTcGVjaWZpY2F0aW9uPgogICAgICAgICA8ZXhpZkVYOlBob3RvZ3JhcGhpY1NlbnNpdGl2aXR5PjE2MDA8L2V4aWZFWDpQaG90b2dyYXBoaWNTZW5zaXRpdml0eT4KICAgICAgICAgPGV4aWZFWDpTZW5zaXRpdml0eVR5cGU+MjwvZXhpZkVYOlNlbnNpdGl2aXR5VHlwZT4KICAgICAgICAgPGV4aWZFWDpSZWNvbW1lbmRlZEV4cG9zdXJlSW5kZXg+MTYwMDwvZXhpZkVYOlJlY29tbWVuZGVkRXhwb3N1cmVJbmRleD4KICAgICAgICAgPGV4aWZFWDpMZW5zTW9kZWw+U29ueSBFIDE4LTIwMG1tIEYzLjXigJM2LjMgT1NTIExFIChTRUwxODIwMExFKTwvZXhpZkVYOkxlbnNNb2RlbD4KICAgICAgICAgPGRjOmRlc2NyaXB0aW9uPgogICAgICAgICAgICA8cmRmOkFsdD4KICAgICAgICAgICAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ij5NYWRlaXJhIDIwMTg8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6QWx0PgogICAgICAgICA8L2RjOmRlc2NyaXB0aW9uPgogICAgICAgICA8ZGM6c3ViamVjdD4KICAgICAgICAgICAgPHJkZjpCYWc+CiAgICAgICAgICAgICAgIDxyZGY6bGk+bWFkZWlyYTwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpPnR1bm5lbDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpCYWc+CiAgICAgICAgIDwvZGM6c3ViamVjdD4KICAgICAgICAgPGRjOmNyZWF0b3I+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPkVyaWsgVGhlb2JvbGR0PC9yZGY6bGk+CiAgICAgICAgICAgIDwvcmRmOlNlcT4KICAgICAgICAgPC9kYzpjcmVhdG9yPgogICAgICAgICA8ZGM6cmlnaHRzPgogICAgICAgICAgICA8cmRmOkFsdD4KICAgICAgICAgICAgICAgPHJkZjpsaSB4bWw6bGFuZz0ieC1kZWZhdWx0Ij5FcmlrIFRoZW9ib2xkdDwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpBbHQ+CiAgICAgICAgIDwvZGM6cmlnaHRzPgogICAgICAgICA8eG1wOkNyZWF0b3JUb29sPkNhcHR1cmUgT25lIDExIE1hY2ludG9zaDwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOlJhdGluZz4yPC94bXA6UmF0aW5nPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxOC0xMS0wMVQxNjoyODo1NTwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDxleGlmOkdQU1NwZWVkUmVmPks8L2V4aWY6R1BTU3BlZWRSZWY+CiAgICAgICAgIDxleGlmOlNjZW5lQ2FwdHVyZVR5cGU+MDwvZXhpZjpTY2VuZUNhcHR1cmVUeXBlPgogICAgICAgICA8ZXhpZjpFeGlmVmVyc2lvbj4wMjMwPC9leGlmOkV4aWZWZXJzaW9uPgogICAgICAgICA8ZXhpZjpFeHBvc3VyZUJpYXNWYWx1ZT4tMy8xMDwvZXhpZjpFeHBvc3VyZUJpYXNWYWx1ZT4KICAgICAgICAgPGV4aWY6QnJpZ2h0bmVzc1ZhbHVlPi04ODkvNjQwPC9leGlmOkJyaWdodG5lc3NWYWx1ZT4KICAgICAgICAgPGV4aWY6R1BTU3BlZWQ+MC8xPC9leGlmOkdQU1NwZWVkPgogICAgICAgICA8ZXhpZjpHUFNEYXRlPjIwMTg6MTE6MDE8L2V4aWY6R1BTRGF0ZT4KICAgICAgICAgPGV4aWY6Rm9jYWxMZW5ndGg+MTgvMTwvZXhpZjpGb2NhbExlbmd0aD4KICAgICAgICAgPGV4aWY6R1BTSW1nRGlyZWN0aW9uUmVmPlQ8L2V4aWY6R1BTSW1nRGlyZWN0aW9uUmVmPgogICAgICAgICA8ZXhpZjpHUFNJbWdEaXJlY3Rpb24+MC8xPC9leGlmOkdQU0ltZ0RpcmVjdGlvbj4KICAgICAgICAgPGV4aWY6TGlnaHRTb3VyY2U+MDwvZXhpZjpMaWdodFNvdXJjZT4KICAgICAgICAgPGV4aWY6RXhwb3N1cmVQcm9ncmFtPjM8L2V4aWY6RXhwb3N1cmVQcm9ncmFtPgogICAgICAgICA8ZXhpZjpGb2NhbExlbkluMzVtbUZpbG0+Mjc8L2V4aWY6Rm9jYWxMZW5JbjM1bW1GaWxtPgogICAgICAgICA8ZXhpZjpGbGFzaCByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxleGlmOkZ1bmN0aW9uPkZhbHNlPC9leGlmOkZ1bmN0aW9uPgogICAgICAgICAgICA8ZXhpZjpGaXJlZD5GYWxzZTwvZXhpZjpGaXJlZD4KICAgICAgICAgICAgPGV4aWY6UmV0dXJuPjA8L2V4aWY6UmV0dXJuPgogICAgICAgICAgICA8ZXhpZjpNb2RlPjI8L2V4aWY6TW9kZT4KICAgICAgICAgICAgPGV4aWY6UmVkRXllTW9kZT5GYWxzZTwvZXhpZjpSZWRFeWVNb2RlPgogICAgICAgICA8L2V4aWY6Rmxhc2g+CiAgICAgICAgIDxleGlmOlNoYXJwbmVzcz4wPC9leGlmOlNoYXJwbmVzcz4KICAgICAgICAgPGV4aWY6U2F0dXJhdGlvbj4wPC9leGlmOlNhdHVyYXRpb24+CiAgICAgICAgIDxleGlmOk1ldGVyaW5nTW9kZT41PC9leGlmOk1ldGVyaW5nTW9kZT4KICAgICAgICAgPGV4aWY6Rk51bWJlcj41LzE8L2V4aWY6Rk51bWJlcj4KICAgICAgICAgPGV4aWY6Q29udHJhc3Q+MDwvZXhpZjpDb250cmFzdD4KICAgICAgICAgPGV4aWY6TWF4QXBlcnR1cmVWYWx1ZT40NjMvMTI4PC9leGlmOk1heEFwZXJ0dXJlVmFsdWU+CiAgICAgICAgIDxleGlmOlNodXR0ZXJTcGVlZFZhbHVlPjYxMzk0LzE2NTkxPC9leGlmOlNodXR0ZXJTcGVlZFZhbHVlPgogICAgICAgICA8ZXhpZjpJU09TcGVlZFJhdGluZ3M+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPjE2MDA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L2V4aWY6SVNPU3BlZWRSYXRpbmdzPgogICAgICAgICA8ZXhpZjpGaWxlU291cmNlPjM8L2V4aWY6RmlsZVNvdXJjZT4KICAgICAgICAgPGV4aWY6R1BTQWx0aXR1ZGVSZWY+MDwvZXhpZjpHUFNBbHRpdHVkZVJlZj4KICAgICAgICAgPGV4aWY6R1BTTGF0aXR1ZGU+MzIsNDEuODYzN048L2V4aWY6R1BTTGF0aXR1ZGU+CiAgICAgICAgIDxleGlmOkV4cG9zdXJlTW9kZT4wPC9leGlmOkV4cG9zdXJlTW9kZT4KICAgICAgICAgPGV4aWY6UGl4ZWxZRGltZW5zaW9uPjM4NDA8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICAgICA8ZXhpZjpFeHBvc3VyZVRpbWU+MS8xMzwvZXhpZjpFeHBvc3VyZVRpbWU+CiAgICAgICAgIDxleGlmOkdQU0xvbmdpdHVkZT4xNyw3Ljc0OTVXPC9leGlmOkdQU0xvbmdpdHVkZT4KICAgICAgICAgPGV4aWY6Q3VzdG9tUmVuZGVyZWQ+MDwvZXhpZjpDdXN0b21SZW5kZXJlZD4KICAgICAgICAgPGV4aWY6U2NlbmVUeXBlPjE8L2V4aWY6U2NlbmVUeXBlPgogICAgICAgICA8ZXhpZjpQaXhlbFhEaW1lbnNpb24+NTc2MDwvZXhpZjpQaXhlbFhEaW1lbnNpb24+CiAgICAgICAgIDxleGlmOldoaXRlQmFsYW5jZT4wPC9leGlmOldoaXRlQmFsYW5jZT4KICAgICAgICAgPGV4aWY6QXBlcnR1cmVWYWx1ZT4yNzQ2MzMvNTkxMzk8L2V4aWY6QXBlcnR1cmVWYWx1ZT4KICAgICAgICAgPGV4aWY6R1BTQWx0aXR1ZGU+MC8xPC9leGlmOkdQU0FsdGl0dWRlPgogICAgICAgICA8ZXhpZjpEaWdpdGFsWm9vbVJhdGlvPjEvMTwvZXhpZjpEaWdpdGFsWm9vbVJhdGlvPgogICAgICAgICA8ZXhpZjpHUFNIUG9zaXRpb25pbmdFcnJvcj4wLzE8L2V4aWY6R1BTSFBvc2l0aW9uaW5nRXJyb3I+CiAgICAgICAgIDxhdXg6Rmxhc2hDb21wZW5zYXRpb24+MC8xPC9hdXg6Rmxhc2hDb21wZW5zYXRpb24+CiAgICAgICAgIDxhdXg6TGVucz5Tb255IEUgMTgtMjAwbW0gRjMuNeKAkzYuMyBPU1MgTEUgKFNFTDE4MjAwTEUpPC9hdXg6TGVucz4KICAgICAgICAgPGF1eDpMZW5zSW5mbz4xOC8xIDE4LzEgODA5NzIvMjMxMTUgODA5NzIvMjMxMTU8L2F1eDpMZW5zSW5mbz4KICAgICAgICAgPHBob3Rvc2hvcDpEYXRlQ3JlYXRlZD4yMDE4LTExLTAxVDE2OjI4OjU1PC9waG90b3Nob3A6RGF0ZUNyZWF0ZWQ+CiAgICAgICAgIDx0aWZmOlJlc29sdXRpb25Vbml0PjI8L3RpZmY6UmVzb2x1dGlvblVuaXQ+CiAgICAgICAgIDx0aWZmOk1ha2U+U09OWTwvdGlmZjpNYWtlPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpNb2RlbD5JTENFLTY1MDA8L3RpZmY6TW9kZWw+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgojArmIAAADA0lEQVQ4ER2TTW/cRBjHf2OPvfZ637LdbFhl06LSEFqJigPiUiFBxIUDB74Cn4QTQuJLIG5U6hfogQokRFNBi6hESqNQpVRqUpIsG3v9Ph4ed6SRRzOeef5vj/ri8w/sb48PGV+a4jmW2jR0PE2SxKyynI1RSBQG9AOFS8XTk5q41hSl4dW/p7jzjdGXFgfd7TMMNTQG1dSMI42Vb15UZGWNo8B1HRrZq+mwql151MGpGpiM+1BlHL04pusWXJ+HTHqKtRC6oS+XLElu+Oe8lnPDZj+B+ABTyQMyOFvEVHlG7UWM+x123shYC0pGkc8oMES6YhGXdIIOt39+ybUb7/L9t19x480XaN9VBFoqlBCalPVJj9+PwJQax7VcilxuXumRFjkPj0O2ga3piMmoLxQQ8jKG/ZBZsOSz3W0+vHWd5fKCk8Ty/DTj8LQkzuCtnSlRkNJ/exu7/IPb333DT/uXcYQexirqlws++Wib7mCTxz+ecHT8Si7mpFXJ13cOiEZzrmytkxwecP+pw90Hir5fopVysHVO1pvx958PWMYpznyd3VtXefTrHraA3fe32Nt7xvnKkI99kszieT7WnKEdR9wVK8Jhwy/3z1kkFaNJxeVpRLlzk83ZiOPzhCd/7TNdn/G8GfCxV4lzGgGJM4i6DLsekSchQmHkQdWU3Lv7A8qUJKLHM4Hd6Al5qfh0ZyyZUHguvHdtjraigWQEV1KYS7qWaUnVeCTegP3DJwgAEW8GteUit2hxrQ1ULOtatNOVgaIwaBrZLPG1Zjpo7ZGD7oRUCnhWchFGYnUjKI2ESnIkKHKhruNMKgo0z1YMox5dX+BYl7rxcVWDkh+L9kdV8M6GJi0r8lribKWkTJ3FZ4SuRFT7FHWDL9xqowQPdGRtBaanPS6yVND5rPV8TNOQpCtcoaoj5z/pQlgJjagjIjaOeN9ybS/XMl1ZS7PpQHohZpGK6FFHCvivbX1t4yqX11zpRBmp8Gx1aeFXonCLpA1b27FKuZzFiVBwCHxNT7rxf6wZb9PFiNSWAAAAAElFTkSuQmCC
PNG;

        return base64_decode($image);
    }

    public function testStringCastEmptyData(): void
    {
        $image = new Image(null);
        $this->assertEquals('', (string)$image);
    }

    public function testResourceRead(): void
    {
        $temp = tmpfile();
        fwrite($temp, self::provideImageInput());
        fseek($temp, 0);
        $image = new Image($temp);
        $this->assertEquals(self::provideImageInput(), (string)$image);

        fclose($temp);
    }

    public function testStringCastString(): void
    {
        $image = new Image('test');
        $this->assertEquals('test', (string)$image);
    }
    
    public function testUnexpectedMode(): void
    {
        $temp = tmpfile();
        fwrite($temp, self::provideImageInput());
        fseek($temp, 0);

        $r = new ResizeImageRequest($temp, null, 100, 'unknown', 100);
        $s = new ImagineResizeService(new NullLogger());
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unavailable resize mode requested');
        
        $s->resize($r);
    }

}
