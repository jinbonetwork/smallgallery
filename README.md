smallgallery
============

슬라이드 쇼 형식의 소규모 갤러리를 위한 [워드프레스](http://wordpress.org) 테마입니다.

개요
----

1. 게시물을 슬라이드로 취급합니다.
    * 슬라이드들은 날짜 순으로 정렬됩니다. (가장 최근 게시물이 마지막 슬라이드가 됨)
    * 포스트 포맷을 `image`로 설정하면 이미지 슬라이드로 간주하고 피쳐 이미지를 출력합니다.
	* 포스트 포맷을 `standard`로 설정하면 텍스트 슬라이드로 간주하고 피쳐 이미지를 감춥니다. (썸네일 아카이브에서만 피쳐 이미지를 사용)

2. 워드프레스 메뉴 기능을 지원합니다.

3. 카테고리/태그/저자 정보는 갤러리 구성에 관여하지 않습니다.
    * (미구현) 카테고리/태그/저자 및 날짜 아카이브는 썸네일 갤러리로 출력됩니다.

4. 슬라이드 디자인을 위한 추가 설정을 제공합니다. (기본값은 `_config.php` 파일에서 설정)
    * 제목, 본문, 날짜, 카테고리, 태그, 저자 정보를 게시물 단위로 보여주거나 감출 수 있습니다.
	* 슬라이드 무게(중요도)를 설정할 수 있습니다.

5. (미구현) `thumbnailarchives` 숏코드로 썸네일 갤러리 형식의 아카이브를 출력할 수 있습니다. 지정할 수 있는 옵션은 다음과 같습니다.
    * `size` -- 썸네일 사이즈를 지정합니다. (기본값: thumbnail)
	* `category` -- 특정 카테고리에 속한 게시물로 아카이브를 구성합니다. [기본값: 없음/현재 카테고리(카테고리 아카이브일 때)]
	* `tag` -- 특정 태그에 속한 게시물로 아카이브를 구성합니다. [기본값: 없음/현재 태그(태그 아카이브일 때)]
	* `author` -- 특정 저자의 게시물로 아카이브를 구성합니다. [기본값: 없음/현재 저자(저자 아카이브일 때)]
	* `date` -- 특정 날짜에 속한 게시물로 아카이브를 구성합니다. [기본값: 없음/현재 날짜(날짜 아카이브일 때)]
	* `ids` -- 지정된 게시물로 아카이브를 구성합니다. *이 값이 지정되면 category, tag, author, date 옵션을 무시합니다.* (기본값: 없음)

할일
----

1. fancybox로 캡션 오버레이 처리
    * <del>`.caption` 블록이 존재하는 `.format-image` 슬라이드에서만 동작해야 함</del>
	* <del>각 필드의 설정 양식 변경 -- `켜기/끄기`에서 `레이블/캡션/끄기`로</del>
2. 썸네일 아카이브 숏코드 작성
    * <del>이 기능을 사용해서 아카이브 템플릿 작성</del>
3. 기타 
    * <del>번역 파일 작성</del>
    * SNS 공유 기능
	* 디자인 마감
4. 오류
    * 내부 링크 AJAX 처리
    * 리사이즈 제대로 안되는 문제
5. UI
	* <del>처음으로 접속하면 도움말 출력</del>
    * <del>재스쳐 -- 스와이프</del>
	* 애니메이션 -- 코너링
	* 덧글 기능
